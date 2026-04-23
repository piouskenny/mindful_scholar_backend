<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Services\BedrockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected BedrockService $bedrockService;

    public function __construct(BedrockService $bedrockService)
    {
        $this->bedrockService = $bedrockService;
    }

    /**
     * Send a message and get AI response.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $userMessage = $validated['message'];

        // Save user message
        $userMsg = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $userMessage,
            'is_bot' => false,
        ]);

        // Build context from user's tasks and exams
        $context = [
            'tasks' => $user->tasks()
                ->where('is_completed', false)
                ->select('title', 'course_code', 'priority', 'due_date')
                ->get()
                ->toArray(),
            'exams' => $user->exams()
                ->where('exam_date', '>=', now())
                ->select('course_code', 'course_name', 'exam_date', 'venue')
                ->get()
                ->toArray(),
        ];

        // Get chat history for context
        $chatHistory = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'message' => $msg->message,
                    'is_bot' => $msg->is_bot,
                ];
            })
            ->values()
            ->toArray();

        // Get AI response
        $aiResponse = $this->bedrockService->chat($userMessage, $context, $chatHistory);

        // Save bot response
        $botMsg = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $aiResponse,
            'is_bot' => true,
        ]);

        return response()->json([
            'user_message' => [
                'id' => $userMsg->id,
                'message' => $userMsg->message,
                'is_bot' => false,
                'created_at' => $userMsg->created_at->toIso8601String(),
            ],
            'bot_message' => [
                'id' => $botMsg->id,
                'message' => $botMsg->message,
                'is_bot' => true,
                'created_at' => $botMsg->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get chat history.
     */
    public function history(Request $request): JsonResponse
    {
        $messages = ChatMessage::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_bot' => $msg->is_bot,
                    'created_at' => $msg->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Clear chat history.
     */
    public function clear(Request $request): JsonResponse
    {
        ChatMessage::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Chat history cleared',
        ]);
    }
}

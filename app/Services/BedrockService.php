<?php

namespace App\Services;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Support\Facades\Log;

class BedrockService
{
    protected ?BedrockRuntimeClient $client = null;

    protected function getClient(): BedrockRuntimeClient
    {
        if ($this->client === null) {
            $this->client = new BedrockRuntimeClient([
                'region' => config('services.aws_bedrock.region', env('AWS_DEFAULT_REGION', 'us-east-1')),
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);
        }

        return $this->client;
    }

    /**
     * Send a message to AWS Bedrock via API Gateway and get AI response.
     *
     * @param string $userMessage The user's message
     * @param array $context Additional context (tasks, exams, etc.)
     * @param array $chatHistory Previous chat messages for context
     * @return string The AI response
     */
    public function chat(string $userMessage, array $context = [], array $chatHistory = []): string
    {
        try {
            $url = env('BEDROCK_CHAT_URL');
            
            if (!$url) {
                Log::warning('BEDROCK_CHAT_URL not set in .env');
                return $this->getFallbackResponse($userMessage);
            }

            $systemPrompt = $this->buildSystemPrompt($context);
            $messages = $this->buildMessages($chatHistory, $userMessage);

            $response = \Illuminate\Support\Facades\Http::post($url, [
                'system_prompt' => $systemPrompt,
                'messages' => $messages,
                'user_message' => $userMessage, // Optional, depending on what the Lambda expects
            ]);

            if ($response->successful()) {
                $result = $response->json();
                // Adjust this based on your Lambda's actual response structure
                // Assuming it returns {'response': '...'} or similar
                return $result['response'] ?? $result['message'] ?? $result['content'][0]['text'] ?? 'No response received from AI.';
            }

            Log::error('Bedrock API Gateway Error: ' . $response->status() . ' - ' . $response->body());
            return $this->getFallbackResponse($userMessage);

        } catch (\Exception $e) {
            Log::error('Bedrock Service Exception: ' . $e->getMessage());
            return $this->getFallbackResponse($userMessage);
        }
    }

    /**
     * Build the system prompt with academic context.
     */
    protected function buildSystemPrompt(array $context): string
    {
        $prompt = "You are Mindful AI, a friendly and supportive academic companion for university students. ";
        $prompt .= "You help with study questions, time management, exam preparation, and emotional support. ";
        $prompt .= "Keep responses concise, encouraging, and practical. Use clear formatting when explaining concepts. ";
        $prompt .= "If a student seems stressed, acknowledge their feelings and offer calming advice alongside academic help.";

        if (!empty($context['tasks'])) {
            $prompt .= "\n\nThe student's current tasks: " . json_encode($context['tasks']);
        }

        if (!empty($context['exams'])) {
            $prompt .= "\n\nThe student's upcoming exams: " . json_encode($context['exams']);
        }

        return $prompt;
    }

    /**
     * Build message array from chat history.
     */
    protected function buildMessages(array $chatHistory, string $userMessage): array
    {
        $messages = [];

        // Add recent chat history (last 10 messages for context)
        $recentHistory = array_slice($chatHistory, -10);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['is_bot'] ? 'assistant' : 'user',
                'content' => $msg['message'],
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    /**
     * Provide a fallback response when AWS Bedrock is unavailable.
     */
    protected function getFallbackResponse(string $userMessage): string
    {
        $lowerMessage = strtolower($userMessage);

        if (str_contains($lowerMessage, 'stress') || str_contains($lowerMessage, 'anxious') || str_contains($lowerMessage, 'worried')) {
            return "I understand you're feeling stressed. That's completely normal, especially around exam time. "
                . "Try taking a few deep breaths — inhale for 4 seconds, hold for 4, exhale for 4. "
                . "Remember: small, consistent steps lead to big results. You've got this! 💪";
        }

        if (str_contains($lowerMessage, 'study') || str_contains($lowerMessage, 'plan')) {
            return "Great that you're thinking about your study plan! Here's a quick approach:\n\n"
                . "1. List your upcoming exams by date\n"
                . "2. Allocate more time to subjects you find challenging\n"
                . "3. Use the Pomodoro technique: 25 min study, 5 min break\n"
                . "4. Review notes before bed — it helps with memory consolidation\n\n"
                . "Would you like me to help with a specific subject?";
        }

        return "I'm here to help with your studies! I can assist with:\n\n"
            . "📚 Explaining academic concepts\n"
            . "📅 Creating study plans\n"
            . "🧘 Stress management tips\n"
            . "⏰ Time management advice\n\n"
            . "What would you like help with?";
    }
}

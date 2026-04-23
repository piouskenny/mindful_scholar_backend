<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * List all tasks for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->tasks()->orderBy('due_date', 'asc');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('is_completed', false);
            } elseif ($request->status === 'done') {
                $query->where('is_completed', true);
            }
        }

        $tasks = $query->get();
        $totalCount = $request->user()->tasks()->count();
        $completedCount = $request->user()->tasks()->where('is_completed', true)->count();

        return response()->json([
            'tasks' => $tasks,
            'total' => $totalCount,
            'completed' => $completedCount,
        ]);
    }

    /**
     * Create a new task.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'priority' => 'required|in:high,medium,low',
            'due_date' => 'required|date',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Update a task.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        // Ensure task belongs to user
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'priority' => 'sometimes|in:high,medium,low',
            'due_date' => 'sometimes|date',
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task->fresh(),
        ]);
    }

    /**
     * Delete a task.
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Toggle task completion.
     */
    public function toggle(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update(['is_completed' => !$task->is_completed]);

        return response()->json([
            'message' => 'Task status toggled',
            'task' => $task->fresh(),
        ]);
    }

    /**
     * Get today's tasks for the dashboard.
     */
    public function today(Request $request): JsonResponse
    {
        $tasks = $request->user()->tasks()
            ->where('due_date', '<=', now()->addDay()->toDateString())
            ->orderBy('is_completed', 'asc')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }
}

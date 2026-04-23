<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * List all exams for authenticated user, sorted by date.
     */
    public function index(Request $request): JsonResponse
    {
        $exams = $request->user()->exams()
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date', 'asc')
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'course_code' => $exam->course_code,
                    'course_name' => $exam->course_name,
                    'exam_date' => $exam->exam_date->toIso8601String(),
                    'exam_date_formatted' => $exam->exam_date->format('M d · g:i A'),
                    'venue' => $exam->venue,
                    'days_left' => $exam->days_left,
                    'urgency' => $exam->urgency,
                ];
            });

        $totalThisSemester = $request->user()->exams()->count();

        return response()->json([
            'exams' => $exams,
            'total_this_semester' => $totalThisSemester,
        ]);
    }

    /**
     * Create a new exam.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:50',
            'course_name' => 'required|string|max:255',
            'exam_date' => 'required|date|after:now',
            'venue' => 'nullable|string|max:255',
        ]);

        $exam = $request->user()->exams()->create($validated);

        return response()->json([
            'message' => 'Exam added successfully',
            'exam' => [
                'id' => $exam->id,
                'course_code' => $exam->course_code,
                'course_name' => $exam->course_name,
                'exam_date' => $exam->exam_date->toIso8601String(),
                'exam_date_formatted' => $exam->exam_date->format('M d · g:i A'),
                'venue' => $exam->venue,
                'days_left' => $exam->days_left,
                'urgency' => $exam->urgency,
            ],
        ], 201);
    }

    /**
     * Update an exam.
     */
    public function update(Request $request, Exam $exam): JsonResponse
    {
        if ($exam->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'course_code' => 'sometimes|string|max:50',
            'course_name' => 'sometimes|string|max:255',
            'exam_date' => 'sometimes|date|after:now',
            'venue' => 'nullable|string|max:255',
        ]);

        $exam->update($validated);
        $exam = $exam->fresh();

        return response()->json([
            'message' => 'Exam updated successfully',
            'exam' => [
                'id' => $exam->id,
                'course_code' => $exam->course_code,
                'course_name' => $exam->course_name,
                'exam_date' => $exam->exam_date->toIso8601String(),
                'exam_date_formatted' => $exam->exam_date->format('M d · g:i A'),
                'venue' => $exam->venue,
                'days_left' => $exam->days_left,
                'urgency' => $exam->urgency,
            ],
        ]);
    }

    /**
     * Delete an exam.
     */
    public function destroy(Request $request, Exam $exam): JsonResponse
    {
        if ($exam->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $exam->delete();

        return response()->json([
            'message' => 'Exam deleted successfully',
        ]);
    }

    /**
     * Get upcoming exams for dashboard (next 2).
     */
    public function upcoming(Request $request): JsonResponse
    {
        $exams = $request->user()->exams()
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date', 'asc')
            ->limit(2)
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'course_code' => $exam->course_code,
                    'days_left' => $exam->days_left,
                ];
            });

        return response()->json([
            'exams' => $exams,
        ]);
    }
}

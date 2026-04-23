<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Timetable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * List all exams for authenticated user, including school timetable.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Custom user exams
        $customExams = $user->exams()
            ->where('exam_date', '>=', now())
            ->get()
            ->map(function ($exam) {
                return $this->formatExam($exam, true);
            });

        // School timetable exams
        $timetableExams = collect();
        if ($user->school_id && $user->level) {
            // Flexible level matching (handles '400' vs '400L')
            $levelBase = preg_replace('/[^0-9]/', '', $user->level);
            
            $timetableExams = Timetable::where('school_id', $user->school_id)
                ->where(function($q) use ($user, $levelBase) {
                    $q->where('level', $user->level)
                      ->orWhere('level', 'like', $levelBase . '%')
                      ->orWhere('level', 'like', '%' . $levelBase);
                })
                ->where('exam_date', '>=', now())
                ->get()
                ->map(function ($exam) {
                    return $this->formatExam($exam, false);
                });
        }

        $allExams = $customExams->concat($timetableExams)->sortBy('exam_date')->values();

        return response()->json([
            'exams' => $allExams,
            'total_this_semester' => $allExams->count(),
        ]);
    }

    private function formatExam($exam, $isCustom)
    {
        $date = Carbon::parse($exam->exam_date);
        $daysLeft = (int) ceil(now()->diffInDays($date, false));
        
        return [
            'id' => $exam->id,
            'course_code' => $exam->course_code,
            'course_name' => $exam->course_name,
            'exam_date' => $date->toIso8601String(),
            'exam_date_formatted' => $date->format('M d · g:i A'),
            'venue' => $exam->venue,
            'days_left' => $daysLeft < 0 ? 0 : $daysLeft,
            'urgency' => $daysLeft <= 3 ? 'high' : ($daysLeft <= 7 ? 'medium' : 'low'),
            'is_custom' => $isCustom,
        ];
    }

    /**
     * Get upcoming exams for dashboard (next 2).
     */
    public function upcoming(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $customExams = $user->exams()
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date', 'asc')
            ->limit(2)
            ->get();

        $timetableExams = collect();
        if ($user->school_id && $user->level) {
            $levelBase = preg_replace('/[^0-9]/', '', $user->level);
            
            $timetableExams = Timetable::where('school_id', $user->school_id)
                ->where(function($q) use ($user, $levelBase) {
                    $q->where('level', $user->level)
                      ->orWhere('level', 'like', $levelBase . '%')
                      ->orWhere('level', 'like', '%' . $levelBase);
                })
                ->where('exam_date', '>=', now())
                ->orderBy('exam_date', 'asc')
                ->limit(2)
                ->get();
        }

        $exams = $customExams->concat($timetableExams)
            ->sortBy('exam_date')
            ->take(2)
            ->map(function ($exam) {
                $date = Carbon::parse($exam->exam_date);
                return [
                    'id' => $exam->id,
                    'course_code' => $exam->course_code,
                    'days_left' => (int) ceil(now()->diffInDays($date, false)),
                ];
            });

        return response()->json([
            'exams' => $exams->values(),
        ]);
    }

    /**
     * Create a new custom exam.
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
            'exam' => $this->formatExam($exam, true),
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

        return response()->json([
            'message' => 'Exam updated successfully',
            'exam' => $this->formatExam($exam->fresh(), true),
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
}

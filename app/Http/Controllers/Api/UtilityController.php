<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affirmation;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    /**
     * Get the daily affirmation.
     */
    public function dailyAffirmation(): JsonResponse
    {
        // Simple logic: pick an affirmation based on the day of the year
        $dayOfYear = date('z');
        $count = Affirmation::count();
        
        if ($count == 0) {
            return response()->json([
                'affirmation' => [
                    'text' => 'Focus on the process, not just the outcome.',
                    'author' => 'Mindful Scholar'
                ]
            ]);
        }

        $index = $dayOfYear % $count;
        $affirmation = Affirmation::skip($index)->first();

        return response()->json([
            'affirmation' => [
                'text' => $affirmation->text,
                'author' => $affirmation->author ?? 'Mindful Scholar'
            ]
        ]);
    }

    /**
     * Get notifications for the user.
     */
    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Fetch global notifications (school_id is null) 
        // OR notifications specifically for the user's school
        $notifications = Notification::whereNull('school_id')
            ->orWhere('school_id', $user->school_id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}

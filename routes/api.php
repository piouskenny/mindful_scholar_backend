<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where the API routes for Mindful Scholar are registered.
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::get('/schools', [AuthController::class, 'schools']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    Route::post('/user/profile-picture', [AuthController::class, 'uploadProfilePicture']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/today', [TaskController::class, 'today']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle']);
    Route::apiResource('tasks', TaskController::class)->except(['index', 'store']);

    // Exams
    Route::get('/exams', [ExamController::class, 'index']);
    Route::get('/exams/upcoming', [ExamController::class, 'upcoming']);
    Route::apiResource('exams', ExamController::class)->except(['index']);

    // Chat
    Route::get('/chat/history', [ChatController::class, 'history']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::post('/chat/clear', [ChatController::class, 'clear']);

    // Chatbot (New)
    Route::get('/chatbot/history', [ChatbotController::class, 'getHistory']);
    Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage']);
    Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory']);

    // Utilities
    Route::get('/daily-affirmation', [\App\Http\Controllers\Api\UtilityController::class, 'dailyAffirmation']);
    Route::get('/notifications', [\App\Http\Controllers\Api\UtilityController::class, 'notifications']);
});

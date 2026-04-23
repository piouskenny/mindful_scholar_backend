<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// Admin Protected Routes
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/schools', [AdminController::class, 'schools'])->name('admin.schools');
    Route::post('/schools', [AdminController::class, 'storeSchool'])->name('admin.schools.store');
    Route::get('/timetables', [AdminController::class, 'timetables'])->name('admin.timetables');
    Route::post('/timetables', [AdminController::class, 'storeTimetable'])->name('admin.timetables.store');
    
    // Affirmations
    Route::get('/affirmations', [AdminController::class, 'affirmations'])->name('admin.affirmations');
    Route::post('/affirmations', [AdminController::class, 'storeAffirmation'])->name('admin.affirmations.store');

    // Notifications
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::post('/notifications', [AdminController::class, 'storeNotification'])->name('admin.notifications.store');
});

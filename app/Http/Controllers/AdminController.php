<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Timetable;
use App\Models\Affirmation;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $schoolCount = School::count();
        $timetableCount = Timetable::count();
        $affirmationCount = Affirmation::count();
        $notificationCount = Notification::count();
        return view('admin.dashboard', compact('schoolCount', 'timetableCount', 'affirmationCount', 'notificationCount'));
    }

    public function schools()
    {
        $schools = School::all();
        return view('admin.schools', compact('schools'));
    }

    public function storeSchool(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:schools,name',
            'short_name' => 'nullable|string|max:10',
        ]);

        School::create($request->all());
        return back()->with('success', 'School added successfully');
    }

    public function timetables()
    {
        $schools = School::all();
        $timetables = Timetable::with('school')->orderBy('exam_date', 'asc')->get();
        return view('admin.timetables', compact('schools', 'timetables'));
    }

    public function storeTimetable(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'level' => 'required|string',
            'course_code' => 'required|string',
            'course_name' => 'required|string',
            'exam_date' => 'required|date',
            'venue' => 'nullable|string',
        ]);

        Timetable::create($request->all());
        return back()->with('success', 'Timetable record added successfully');
    }

    public function affirmations()
    {
        $affirmations = Affirmation::latest()->get();
        return view('admin.affirmations', compact('affirmations'));
    }

    public function storeAffirmation(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'author' => 'nullable|string',
        ]);

        Affirmation::create($request->all());
        return back()->with('success', 'Affirmation added successfully');
    }

    public function notifications()
    {
        $schools = School::all();
        $notifications = Notification::with('school')->latest()->get();
        return view('admin.notifications', compact('schools', 'notifications'));
    }

    public function storeNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,alert,news',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        Notification::create($request->all());
        return back()->with('success', 'Notification broadcasted successfully');
    }
}

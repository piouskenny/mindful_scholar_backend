<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Timetable;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $schoolCount = School::count();
        $timetableCount = Timetable::count();
        return view('admin.dashboard', compact('schoolCount', 'timetableCount'));
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
}

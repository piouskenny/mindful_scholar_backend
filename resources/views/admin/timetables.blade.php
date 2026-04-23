@extends('layouts.admin')

@section('title', 'Manage Timetables')

@section('content')
<div class="space-y-8">
    <!-- Add Timetable Record Form -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold mb-6">Add New Timetable Record</h3>
        <form action="{{ route('admin.timetables.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select School</label>
                    <select name="school_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none">
                        <option value="">Choose a school...</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <input type="text" name="level" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none" placeholder="e.g. 100L, 200L">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course Code</label>
                    <input type="text" name="course_code" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none" placeholder="e.g. CSC 101">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course Name</label>
                    <input type="text" name="course_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none" placeholder="e.g. Introduction to Computing">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exam Date & Time</label>
                    <input type="datetime-local" name="exam_date" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                    <input type="text" name="venue" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none" placeholder="e.g. Hall A">
                </div>
                <div class="md:col-span-2 lg:col-span-1 flex items-end">
                    <button type="submit" class="w-full py-2.5 bg-[#12372A] text-white rounded-lg font-semibold hover:bg-opacity-90 transition">Add Record</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Timetables List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold">Active Timetables</h3>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">School</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Level</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Course</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Venue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($timetables as $record)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $record->school->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $record->level }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">
                        <span class="font-bold">{{ $record->course_code }}</span> - {{ $record->course_name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $record->exam_date->format('M d, Y @ H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $record->venue }}</td>
                </tr>
                @endforeach
                @if($timetables->isEmpty())
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">No timetable records found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

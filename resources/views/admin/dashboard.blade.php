@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-6">
        <div class="bg-green-100 p-4 rounded-xl text-[#12372A]">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Total Schools</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $schoolCount }}</h3>
        </div>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-6">
        <div class="bg-blue-100 p-4 rounded-xl text-blue-700">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Timetable Records</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $timetableCount }}</h3>
        </div>
    </div>
</div>

<div class="mt-12 bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
    <div class="flex space-x-4">
        <a href="{{ route('admin.schools') }}" class="px-6 py-3 bg-[#12372A] text-white rounded-xl hover:bg-opacity-90 transition">Add New School</a>
        <a href="{{ route('admin.timetables') }}" class="px-6 py-3 border border-[#12372A] text-[#12372A] rounded-xl hover:bg-gray-50 transition">Upload Timetable</a>
    </div>
</div>
@endsection

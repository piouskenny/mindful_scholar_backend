@extends('layouts.admin')

@section('title', 'Manage Notifications')

@section('content')
<div class="space-y-8">
    <!-- Broadcast New Notification -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Broadcast New Notification</h3>
        <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#12372A] focus:border-transparent outline-none transition" placeholder="Enter headline..." required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#12372A] focus:border-transparent outline-none transition" required>
                        <option value="info">General Info</option>
                        <option value="news">School News</option>
                        <option value="alert">Important Alert</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target School (Leave blank for All Schools)</label>
                <select name="school_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#12372A] focus:border-transparent outline-none transition">
                    <option value="">Global Broadcast (All Schools)</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message Body</label>
                <textarea name="message" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#12372A] focus:border-transparent outline-none transition" placeholder="Type your announcement here..." required></textarea>
            </div>
            <button type="submit" class="px-8 py-3 bg-[#12372A] text-white font-semibold rounded-xl hover:bg-opacity-90 transition">Broadcast Notification</button>
        </form>
    </div>

    <!-- Notification History -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-800">Broadcast History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-semibold text-[#12372A]">
                            {{ $notification->school ? $notification->school->name : 'GLOBAL' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $notification->title }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                                @if($notification->type == 'alert') bg-red-100 text-red-700 @elseif($notification->type == 'news') bg-blue-100 text-blue-700 @else bg-gray-100 text-gray-700 @endif">
                                {{ $notification->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $notification->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Manage Schools')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Add School Form -->
    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold mb-6">Add New School</h3>
            <form action="{{ route('admin.schools.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full School Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none" placeholder="e.g. University of Lagos">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Short Name / Abbreviation</label>
                        <input type="text" name="short_name" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#12372A] outline-none" placeholder="e.g. UNILAG">
                    </div>
                    <button type="submit" class="w-full py-3 bg-[#12372A] text-white rounded-lg font-semibold hover:bg-opacity-90 transition">Save School</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Schools List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">School Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Short Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($schools as $school)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $school->id }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $school->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $school->short_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $school->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Manage Affirmations')

@section('content')
<div class="space-y-8">
    <!-- Add New Affirmation -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Add New Affirmation</h3>
        <form action="{{ route('admin.affirmations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Affirmation Text</label>
                <textarea name="text" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#12372A] focus:border-transparent outline-none transition" placeholder="Enter affirmation here..." required></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Author (Optional)</label>
                <input type="text" name="author" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#12372A] focus:border-transparent outline-none transition" placeholder="e.g. Mindful Scholar">
            </div>
            <button type="submit" class="px-8 py-3 bg-[#12372A] text-white font-semibold rounded-xl hover:bg-opacity-90 transition">Save Affirmation</button>
        </form>
    </div>

    <!-- Affirmation List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-800">Existing Affirmations</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Affirmation</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date Added</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($affirmations as $affirmation)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-md">{{ $affirmation->text }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $affirmation->author ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $affirmation->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

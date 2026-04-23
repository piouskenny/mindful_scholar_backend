<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Mindful Scholar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-[#12372A] text-white shadow-xl">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Mindful Scholar</h1>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Admin Dashboard</p>
            </div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded-lg transition hover:bg-white/10 @if(request()->routeIs('admin.dashboard')) bg-white/10 @endif">Dashboard</a>
                <a href="{{ route('admin.schools') }}" class="block px-4 py-2.5 rounded-lg transition hover:bg-white/10 @if(request()->routeIs('admin.schools')) bg-white/10 @endif">Manage Schools</a>
                <a href="{{ route('admin.timetables') }}" class="block px-4 py-2.5 rounded-lg transition hover:bg-white/10 @if(request()->routeIs('admin.timetables')) bg-white/10 @endif">Timetables</a>
                <a href="{{ route('admin.affirmations') }}" class="block px-4 py-2.5 rounded-lg transition hover:bg-white/10 @if(request()->routeIs('admin.affirmations')) bg-white/10 @endif">Affirmations</a>
                <a href="{{ route('admin.notifications') }}" class="block px-4 py-2.5 rounded-lg transition hover:bg-white/10 @if(request()->routeIs('admin.notifications')) bg-white/10 @endif">Notifications</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-700">@yield('title')</h2>
                <div class="flex items-center space-x-6">
                    <div class="text-sm text-gray-500">Welcome, {{ auth()->user()->name ?? 'Admin' }}</div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700 transition">Logout</button>
                    </form>
                </div>
            </header>
            <main class="p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

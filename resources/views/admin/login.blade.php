<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Mindful Scholar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-xl border border-gray-100">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-[#12372A]">Mindful Scholar</h1>
            <p class="text-gray-500 mt-2">Admin Control Center</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" required class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#12372A] outline-none transition" placeholder="admin@example.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#12372A] outline-none transition" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full py-4 bg-[#12372A] text-white rounded-xl font-bold text-lg hover:bg-opacity-90 shadow-lg shadow-green-900/20 transition-all active:scale-[0.98]">
                    Sign In
                </button>
            </div>
        </form>
        
        <div class="mt-8 text-center text-sm text-gray-400">
            Secure Administrator Access Only
        </div>
    </div>
</body>
</html>

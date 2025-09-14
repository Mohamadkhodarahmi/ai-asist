<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Custom Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-[#FDFDFC] dark:bg-[#0a0a0a]">
<div class="w-full max-w-md p-8 bg-white dark:bg-[#161615] rounded-lg shadow-lg animate-fade-in">
    <h1 class="text-2xl font-bold mb-6 text-center text-[#1b1b18] dark:text-[#EDEDEC]">Login to Your Account</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#f53003] transition-all">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input id="password" type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#f53003] transition-all">
        </div>
        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember" class="accent-[#f53003]">
            <label for="remember_me" class="text-sm">Remember me</label>
        </div>
        <button type="submit" class="w-full py-2 px-4 bg-[#f53003] text-white rounded hover:bg-[#c41e00] transition-colors font-semibold shadow">Log In</button>
    </form>
</div>
</body>
</html>

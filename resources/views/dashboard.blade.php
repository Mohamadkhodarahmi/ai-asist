<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-[#FDFDFC] dark:bg-[#0a0a0a]">
<div class="w-full max-w-2xl p-8 bg-white dark:bg-[#161615] rounded-lg shadow-lg animate-fade-in">
    <h1 class="text-2xl font-bold mb-6 text-[#1b1b18] dark:text-[#EDEDEC]">Welcome to your Dashboard, {{ Auth::user()->name }}!</h1>
    <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">This is your protected dashboard area.</p>
    <form method="POST" action="{{ route('logout') }}" class="mb-6">
        @csrf
        <button type="submit" class="py-2 px-4 bg-[#f53003] text-white rounded hover:bg-[#c41e00] transition-colors font-semibold shadow">Log Out</button>
    </form>
    <hr class="my-8 border-[#e3e3e0] dark:border-[#3E3E3A]">
    <a href="{{ route('chat') }}" class="inline-block py-2 px-4 bg-[#1b1b18] text-white rounded hover:bg-black transition-colors font-semibold shadow dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white">Go to AI Chat Assistant</a>
</div>
</body>
</html>

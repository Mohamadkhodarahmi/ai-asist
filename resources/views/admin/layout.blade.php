<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    @stack('styles')
    
    <style>
        .admin-sidebar-link {
            @apply flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200;
        }
        .admin-sidebar-link:hover {
            @apply bg-white/10 dark:bg-white/5;
        }
        .admin-sidebar-link.active {
            @apply bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white shadow-lg;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#FDFDFC] via-[#f8f7f4] to-[#FDFDFC] dark:from-[#0a0a0a] dark:via-[#1a1a1a] dark:to-[#0a0a0a]">
    @include('layouts.navigation')

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 min-h-screen bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border-r border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-[#F53003] to-[#FF4433] bg-clip-text text-transparent">
                    🛠️ Admin Panel
                </h2>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">Management Dashboard</p>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('admin.analytics') }}" class="admin-sidebar-link {{ request()->routeIs('admin.analytics*') ? 'active' : 'text-[#1b1b18] dark:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Analytics</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : 'text-[#1b1b18] dark:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Users</span>
                </a>

                <a href="{{ route('admin.logs.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.logs*') ? 'active' : 'text-[#1b1b18] dark:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Activity Logs</span>
                </a>

                <a href="{{ route('admin.system.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.system*') ? 'active' : 'text-[#1b1b18] dark:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>System Health</span>
                </a>

                <hr class="my-4 border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">

                <a href="{{ route('dashboard') }}" class="admin-sidebar-link text-[#1b1b18] dark:text-[#EDEDEC]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to App</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>


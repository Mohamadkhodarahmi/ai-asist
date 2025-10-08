@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent mb-2">
                User Details
            </h1>
            <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
                Complete user information and activity
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-xl hover:shadow-lg transition-all duration-200">
            ← Back to Users
        </a>
    </div>

    <!-- User Info Card -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-8 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-2xl flex items-center justify-center text-white text-4xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                        {{ $user->name }}
                        @if($user->is_admin)
                            <span class="ml-3 px-3 py-1 bg-purple-500 text-white text-sm rounded-full">Admin</span>
                        @endif
                    </h2>
                    <div class="flex items-center gap-4 text-[#706f6c] dark:text-[#A1A09A]">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $user->email }}
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Joined {{ $user->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="mt-2">
                        @if($user->email_verified_at)
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full text-sm">
                                ✓ Email Verified
                            </span>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 rounded-full text-sm">
                                Email Not Verified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">Total Chats</div>
            <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($chatStats['total_chats']) }}</div>
            <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                {{ number_format($chatStats['last_30_days']) }} in last 30 days
            </div>
        </div>

        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">Documents</div>
            <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($documentStats['total_documents']) }}</div>
            <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                {{ number_format($documentStats['total_questions']) }} questions asked
            </div>
        </div>

        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">Avg Response Time</div>
            <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ $chatStats['avg_response_time'] ? round($chatStats['avg_response_time']) . 'ms' : 'N/A' }}
            </div>
            <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                Performance metric
            </div>
        </div>
    </div>

    <!-- Account Details -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 mb-6">
        <h3 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Account Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-1">User ID</div>
                <div class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">#{{ $user->id }}</div>
            </div>
            <div>
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-1">Current Plan</div>
                <div class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ $user->plan?->name ?? 'No Plan' }}
                </div>
            </div>
            <div>
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-1">Business</div>
                <div class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ $user->business?->name ?? 'No Business' }}
                </div>
            </div>
            <div>
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-1">Member Since</div>
                <div class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ $user->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    @if($user->orders->count() > 0)
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
        <h3 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Recent Orders</h3>
        <div class="space-y-3">
            @foreach($user->orders->take(5) as $order)
                <div class="flex items-center justify-between p-4 bg-[#F53003]/5 rounded-xl">
                    <div>
                        <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                            Order #{{ $order->id }}
                        </div>
                        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $order->created_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                            ${{ number_format($order->amount / 100, 2) }}
                        </div>
                        <span class="px-2 py-1 {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full text-xs">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection


@extends('admin.layout')

@section('title', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent mb-2">
            👥 User Management
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
            Manage all users and their permissions
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['total']) }}</h3>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Total Users</p>
        </div>

        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['admins']) }}</h3>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Admin Users</p>
        </div>

        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['active']) }}</h3>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Active Users (30d)</p>
        </div>

        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['new_today']) }}</h3>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">New Today</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}" 
                placeholder="Search by name or email..."
                class="flex-1 px-4 py-3 bg-white dark:bg-[#0a0a0a] border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-xl text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-[#F53003]/40"
            >
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white rounded-xl hover:shadow-lg hover:shadow-[#F53003]/30 transition-all duration-200 font-medium">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:shadow-lg transition-all duration-200 font-medium">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#F53003]/10">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">User</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Plan</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Joined</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]/50 dark:divide-[#3E3E3A]/50">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#F53003]/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-full flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ $user->name }}
                                            @if($user->is_admin)
                                                <span class="ml-2 px-2 py-1 bg-purple-500 text-white text-xs rounded-full">Admin</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-sm">
                                    {{ $user->plan?->name ?? 'No Plan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->email_verified_at)
                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full text-sm">
                                        ✓ Verified
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 rounded-full text-sm">
                                        Unverified
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $user->created_at->format('M d, Y') }}
                                <div class="text-xs">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="p-2 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="View Details">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <button onclick="toggleAdmin({{ $user->id }}, {{ $user->is_admin ? 'true' : 'false' }})" class="p-2 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg transition-colors" title="Toggle Admin">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                <div class="text-6xl mb-4">👥</div>
                                <div class="text-lg">No users found</div>
                                @if($search)
                                    <div class="text-sm mt-2">Try adjusting your search terms</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleAdmin(userId, isCurrentlyAdmin) {
    if (!confirm(`Are you sure you want to ${isCurrentlyAdmin ? 'revoke' : 'grant'} admin privileges?`)) {
        return;
    }

    fetch(`/admin/users/${userId}/toggle-admin`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Failed to update user: ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred');
        console.error('Error:', error);
    });
}
</script>
@endpush
@endsection


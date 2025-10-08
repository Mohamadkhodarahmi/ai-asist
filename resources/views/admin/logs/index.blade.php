@extends('admin.layout')

@section('title', 'Activity Logs')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent mb-2">
            📋 Activity Logs
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
            Recent user activities and system events
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">Total Chats</div>
            <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['total_chats']) }}</div>
        </div>
        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">Documents</div>
            <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['total_documents']) }}</div>
        </div>
        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">Activities</div>
            <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['total_activities']) }}</div>
        </div>
        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">Today's Chats</div>
            <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($stats['today_chats']) }}</div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-2 mb-6">
        <div class="flex gap-2">
            <a href="{{ route('admin.logs.index', ['type' => 'all']) }}" 
               class="px-4 py-2 rounded-xl transition-all {{ $type === 'all' ? 'bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white' : 'text-[#706f6c] hover:bg-[#F53003]/10' }}">
                All Activities
            </a>
            <a href="{{ route('admin.logs.index', ['type' => 'chat']) }}" 
               class="px-4 py-2 rounded-xl transition-all {{ $type === 'chat' ? 'bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white' : 'text-[#706f6c] hover:bg-[#F53003]/10' }}">
                Chats
            </a>
            <a href="{{ route('admin.logs.index', ['type' => 'documents']) }}" 
               class="px-4 py-2 rounded-xl transition-all {{ $type === 'documents' ? 'bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white' : 'text-[#706f6c] hover:bg-[#F53003]/10' }}">
                Documents
            </a>
            <a href="{{ route('admin.logs.index', ['type' => 'activity']) }}" 
               class="px-4 py-2 rounded-xl transition-all {{ $type === 'activity' ? 'bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white' : 'text-[#706f6c] hover:bg-[#F53003]/10' }}">
                User Activities
            </a>
        </div>
    </div>

    <!-- Logs List -->
    <div class="space-y-4">
        @forelse($logs as $log)
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-12 h-12 {{ $log['type'] === 'chat' ? 'bg-blue-500' : ($log['type'] === 'document' ? 'bg-green-500' : 'bg-purple-500') }} rounded-xl flex items-center justify-center text-white flex-shrink-0">
                            @if($log['type'] === 'chat')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            @elseif($log['type'] === 'document')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $log['user'] }}</span>
                                <span class="px-2 py-1 bg-[#F53003]/10 text-[#F53003] rounded-full text-xs">{{ ucfirst($log['type']) }}</span>
                                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log['created_at']->diffForHumans() }}</span>
                            </div>
                            <div class="text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                {{ $log['description'] }}
                            </div>
                            @if(isset($log['details']) && !empty($log['details']))
                                <div class="mt-3 p-3 bg-[#F53003]/5 rounded-lg">
                                    <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] space-y-1">
                                        @foreach($log['details'] as $key => $value)
                                            <div><span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        {{ $log['created_at']->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-12 text-center">
                <div class="text-6xl mb-4">📋</div>
                <div class="text-lg text-[#706f6c] dark:text-[#A1A09A]">No activity logs found</div>
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">Activity will appear here as users interact with the system</div>
            </div>
        @endforelse
    </div>
</div>
@endsection


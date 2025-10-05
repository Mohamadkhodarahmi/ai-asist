@php
    $user = Auth::user();
    $business = $user->business;
    $userGroups = $user->groups()->with(['messages' => function($query) {
        $query->latest()->limit(5);
    }])->get();
    $totalGroups = $userGroups->count();
    $totalMessages = $userGroups->sum(function($group) {
        return $group->messages->count();
    });
    $recentMessages = $userGroups->flatMap->messages->sortByDesc('created_at')->take(5);
    $ownedGroups = $userGroups->where('pivot.role', 'owner')->count();
@endphp

<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-[#FDFDFC] via-[#f8f7f4] to-[#FDFDFC] dark:from-[#0a0a0a] dark:via-[#1a1a1a] dark:to-[#0a0a0a]">
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                {{-- Welcome Header --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-4xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent mb-2">
                                Welcome back, {{ $user->name }}! 👋
                            </h1>
                            <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
                                Here's what's happening with your AI assistants today.
                            </p>
                        </div>
                        <div class="hidden md:flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistics Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    {{-- Groups Card --}}
                    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">Total Groups</p>
                                <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $totalGroups }}</p>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                    {{ $ownedGroups }} owned
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Messages Card --}}
                    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">Total Messages</p>
                                <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $totalMessages }}</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    All time
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0c0-5 4-9 9-9s9 4 9 9z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Assistant Status Card --}}
                    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">Assistant</p>
                                <p class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $business ? $business->name : 'Not Created' }}
                                </p>
                                <div class="flex items-center gap-1 mt-1">
                                    @if($business && $business->telegram_token)
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        <p class="text-xs text-green-600 dark:text-green-400">Connected</p>
                                    @elseif($business)
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                        <p class="text-xs text-yellow-600 dark:text-yellow-400">Not Connected</p>
                                    @else
                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Not Created</p>
                                    @endif
                                </div>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions Card --}}
                    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">Quick Actions</p>
                                <p class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Get Started</p>
                                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                                    Jump into action
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Main Content --}}
                    <div class="lg:col-span-2 space-y-8">
                        {{-- Assistant Status Section --}}
                        @if ($business)
                            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-8 shadow-lg">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-2xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ $business->name }}</h2>
                                        <div class="flex items-center gap-2">
                                            @if($business->telegram_token)
                                                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                                                <p class="text-sm text-green-600 dark:text-green-400 font-medium">Telegram Bot Connected</p>
                                            @else
                                                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                                <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Telegram Bot Not Connected</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[#706f6c] dark:text-[#A1A09A] mb-6 leading-relaxed">
                                    Your AI assistant is ready to help! Start a conversation, manage your knowledge base, or configure integrations.
                                </p>

                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('chat') }}" class="flex-1 text-center py-4 px-6 bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white rounded-xl hover:shadow-lg hover:shadow-[#F53003]/30 transition-all duration-300 font-semibold hover:scale-[1.02]">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0c0-5 4-9 9-9s9 4 9 9z"/>
                                        </svg>
                                        Go to Chat
                                    </a>
                                    <a href="{{ route('upload') }}" class="flex-1 text-center py-4 px-6 bg-white dark:bg-[#2a2a2a] text-[#1b1b18] dark:text-[#EDEDEC] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl hover:bg-gray-50 dark:hover:bg-[#3a3a3a] transition-all duration-300 font-semibold hover:scale-[1.02]">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        Manage Files
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Create Assistant Section --}}
                            <div class="bg-gradient-to-br from-[#F53003]/10 to-[#FF4433]/10 dark:from-[#F53003]/20 dark:to-[#FF4433]/20 border-2 border-dashed border-[#F53003]/30 dark:border-[#FF4433]/30 rounded-2xl p-12 text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-3">Create Your First AI Assistant</h2>
                                <p class="text-[#706f6c] dark:text-[#A1A09A] mb-8 max-w-md mx-auto leading-relaxed">
                                    Transform any document into your personal AI expert. Upload your knowledge and start chatting with your intelligent assistant.
                                </p>
                                <a href="{{ route('chat') }}" class="inline-flex items-center gap-3 py-4 px-8 bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white rounded-xl hover:shadow-lg hover:shadow-[#F53003]/30 transition-all duration-300 font-semibold hover:scale-[1.02]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Get Started Now
                                </a>
                            </div>
                        @endif

                        {{-- Recent Activity --}}
                        @if($recentMessages->count() > 0)
                            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-8 shadow-lg">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Recent Activity</h3>
                                    <a href="{{ route('groups.index') }}" class="text-sm text-[#F53003] dark:text-[#FF4433] hover:underline font-medium">
                                        View All
                                    </a>
                                </div>
                                <div class="space-y-4">
                                    @foreach($recentMessages as $message)
                                        <div class="flex items-start gap-4 p-4 bg-gray-50/50 dark:bg-[#1a1a1a]/50 rounded-xl hover:bg-gray-100/50 dark:hover:bg-[#2a2a2a]/50 transition-colors">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ strtoupper(substr($message->user?->name ?? 'AI', 0, 1)) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                                                        {{ $message->user?->name ?? 'AI Assistant' }}
                                                    </p>
                                                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                                        {{ $message->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] line-clamp-2">
                                                    {{ Str::limit($message->message, 100) }}
                                                </p>
                                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                    in {{ $message->group->name }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Sidebar --}}
                    <div class="space-y-8">
                        {{-- Quick Actions --}}
                        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                            <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('groups.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition-colors group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Groups</p>
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Manage your groups</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('upload') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition-colors group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Upload Files</p>
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Add knowledge base</p>
                                    </div>
                                </a>

                                <a href="{{ route('pricing') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition-colors group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0-6v2m0 12v2M4 12h2m12 0h2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Pricing</p>
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">View plans</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        {{-- Your Groups --}}
                        @if($userGroups->count() > 0)
                            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Your Groups</h3>
                                    <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $userGroups->count() }}</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($userGroups->take(5) as $group)
                                        <a href="{{ route('groups.chat', $group) }}" class="block p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition-colors group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                                                    {{ strtoupper(substr($group->name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] truncate">{{ $group->name }}</p>
                                                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                                        {{ $group->pivot->role }} • {{ $group->messages->count() }} messages
                                                    </p>
                                                </div>
                                                <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A] group-hover:text-[#F53003] dark:group-hover:text-[#FF4433] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                @if($userGroups->count() > 5)
                                    <div class="mt-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                                        <a href="{{ route('groups.index') }}" class="text-sm text-[#F53003] dark:text-[#FF4433] hover:underline font-medium">
                                            View all {{ $userGroups->count() }} groups
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


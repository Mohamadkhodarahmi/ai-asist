<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Analytics Dashboard</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Track your AI assistant performance and usage patterns</p>
        </div>

        {{-- Filter Controls --}}
        <div class="mb-8 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <label class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Time Period:</label>
                <select wire:model.live="daysFilter" class="px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC]">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Questions --}}
            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Total Questions</p>
                        <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $chatAnalytics['total_questions'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0c0-5 4-9 9-9s9 4 9 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Average Response Time --}}
            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Avg Response Time</p>
                        <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ round(($chatAnalytics['avg_response_time_ms'] ?? 0) / 1000, 1) }}s</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Documents --}}
            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Total Documents</p>
                        <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $documentAnalytics['total_documents'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Tokens --}}
            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Tokens Used</p>
                        <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($chatAnalytics['total_tokens_used'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {{-- Daily Questions Chart --}}
            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Questions Over Time</h3>
                <div class="h-64 flex items-end justify-between gap-2">
                    @if(isset($chatAnalytics['daily_stats']) && $chatAnalytics['daily_stats']->count() > 0)
                        @foreach($chatAnalytics['daily_stats'] as $stat)
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-full bg-[#F53003] rounded-t" style="height: {{ max(20, ($stat->questions_count / max($chatAnalytics['daily_stats']->max('questions_count'), 1)) * 200) }}px;"></div>
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ \Carbon\Carbon::parse($stat->date)->format('M j') }}</span>
                                <span class="text-xs font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $stat->questions_count }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center justify-center h-full w-full text-[#706f6c] dark:text-[#A1A09A]">
                            No data available for the selected period
                        </div>
                    @endif
                </div>
            </div>

            {{-- Most Used Documents --}}
            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Most Used Documents</h3>
                @if(isset($documentAnalytics['most_used_documents']) && $documentAnalytics['most_used_documents']->count() > 0)
                    <div class="space-y-4">
                        @foreach($documentAnalytics['most_used_documents'] as $document)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $document->document_name }}</p>
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $document->document_type }} • {{ round($document->file_size_bytes / 1024, 1) }} KB</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-[#F53003] dark:text-[#FF4433]">{{ $document->questions_asked }}</p>
                                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">questions</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center justify-center h-32 text-[#706f6c] dark:text-[#A1A09A]">
                        No documents uploaded yet
                    </div>
                @endif
            </div>
        </div>

        {{-- Activity Breakdown --}}
        <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Activity Breakdown</h3>
            @if(isset($activityStats['activity_breakdown']) && $activityStats['activity_breakdown']->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($activityStats['activity_breakdown'] as $activity)
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <p class="text-2xl font-bold text-[#F53003] dark:text-[#FF4433]">{{ $activity->count }}</p>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] capitalize">{{ str_replace('_', ' ', $activity->activity_type) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center h-24 text-[#706f6c] dark:text-[#A1A09A]">
                    No activity data available
                </div>
            @endif
        </div>
    </div>
</div>

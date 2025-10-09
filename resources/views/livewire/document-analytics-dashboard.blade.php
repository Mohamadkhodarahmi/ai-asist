<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">📊 Document Analytics</h2>
                <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">Comprehensive insights into your document usage and performance</p>
            </div>

            {{-- Time Period Filter --}}
            <div class="mb-6 flex items-center gap-4">
                <label class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Time Period:</label>
                <select wire:model.live="daysFilter" 
                        class="rounded-lg border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#1E1E1C] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-[#F53003] focus:border-[#F53003]">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>

            {{-- Stats Overview Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Total Documents --}}
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Total Documents</h3>
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                        {{ $documentAnalytics['total_documents'] ?? 0 }}
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        +{{ $documentAnalytics['recent_uploads'] ?? 0 }} recent uploads
                    </p>
                </div>

                {{-- Total Queries --}}
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Total Queries</h3>
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                        {{ number_format($documentAnalytics['total_queries'] ?? 0) }}
                    </p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                        {{ $documentAnalytics['successful_queries'] ?? 0 }} successful
                    </p>
                </div>

                {{-- Success Rate --}}
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Success Rate</h3>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                        {{ $documentAnalytics['success_rate'] ?? 0 }}%
                    </p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                        {{ $documentAnalytics['failed_queries'] ?? 0 }} failed
                    </p>
                </div>

                {{-- Average Response Time --}}
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Avg Response Time</h3>
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                        {{ number_format($documentAnalytics['avg_response_time_ms'] ?? 0) }}ms
                    </p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                        {{ $documentAnalytics['total_size_mb'] ?? 0 }} MB total
                    </p>
                </div>
            </div>

            {{-- Two Column Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Most Used Documents --}}
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">📄 Most Used Documents</h3>
                    
                    @if(!empty($documentAnalytics['most_used_documents']) && count($documentAnalytics['most_used_documents']) > 0)
                        <div class="space-y-4">
                            @foreach($documentAnalytics['most_used_documents'] as $doc)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-[#141414] rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] truncate">
                                            {{ $doc->document_name }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-1">
                                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                                {{ $doc->total_queries ?? 0 }} queries
                                            </span>
                                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                                {{ $doc->document_type }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                {{ round(($doc->successful_queries ?? 0) / max(($doc->total_queries ?? 1), 1) * 100) }}%
                                            </p>
                                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">success</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">No documents uploaded yet</p>
                        </div>
                    @endif
                </div>

                {{-- Document Type Distribution --}}
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">📊 Document Type Performance</h3>
                    
                    @if(!empty($performanceStats['by_type']) && count($performanceStats['by_type']) > 0)
                        <div class="space-y-4">
                            @foreach($performanceStats['by_type'] as $typeData)
                                <div class="p-3 bg-gray-50 dark:bg-[#141414] rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] uppercase">
                                            {{ $typeData['type'] }}
                                        </span>
                                        <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                            {{ $typeData['count'] }} files
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 mt-2">
                                        <div>
                                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Queries</p>
                                            <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                                                {{ number_format($typeData['total_queries']) }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Success Rate</p>
                                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                {{ $typeData['success_rate'] }}%
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Avg Time</p>
                                            <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                                                {{ number_format($typeData['avg_response_time']) }}ms
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">No analytics data available</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top Search Terms --}}
            @if(!empty($documentAnalytics['top_search_terms']) && count($documentAnalytics['top_search_terms']) > 0)
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A] mb-8">
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">🔍 Top Search Terms</h3>
                    
                    <div class="flex flex-wrap gap-2">
                        @foreach($documentAnalytics['top_search_terms'] as $term => $count)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg text-sm">
                                <span class="font-medium">{{ $term }}</span>
                                <span class="text-xs bg-blue-200 dark:bg-blue-800 px-2 py-0.5 rounded-full">{{ $count }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Performance Trend --}}
            @if(!empty($performanceStats['performance_trend']) && count($performanceStats['performance_trend']) > 0)
                <div class="bg-white dark:bg-[#1E1E1C] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">📈 Performance Trend (Last 7 Days)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Active Docs</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Queries</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Avg Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                                @foreach($performanceStats['performance_trend'] as $day)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ $day->active_documents }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ number_format($day->queries) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ number_format($day->avg_time) }}ms
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Storage Info --}}
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            Total Storage Used: {{ $performanceStats['total_storage_mb'] ?? 0 }} MB
                        </p>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                            Your current plan allows for document uploads based on your subscription tier.
                        </p>
                    </div>
                </div>
            </div>
        </div>
</div>

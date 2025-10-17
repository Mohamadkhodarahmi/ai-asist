<div class="py-8 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📊 Telegram Bot Analytics</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Monitor your bot's performance and user engagement</p>
                </div>
                <div class="flex items-center space-x-4">
                    <select wire:model.live="dateRange" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        @foreach($dateRanges as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if(!$bot)
            {{-- No Bot Message --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">No Telegram Bot Found</h3>
                        <p class="text-yellow-700 dark:text-yellow-300 mt-1">
                            You need to create and configure a Telegram bot first. 
                            <a href="{{ route('telegram-bot-builder') }}" class="underline hover:text-yellow-900 dark:hover:text-yellow-100">Create your bot now</a>
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- Bot Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Messages</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analytics['total_messages'] ?? 0) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">AI Responses</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analytics['ai_responses'] ?? 0) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Response Rate</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['response_rate'] ?? 0 }}%</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Response Time</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ round($analytics['avg_response_time'] ?? 0) }}ms</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bot Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bot Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Bot Name</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $botStats['bot_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $botStats['is_active'] ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                            {{ $botStats['is_active'] ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Personality</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ ucfirst($botStats['personality_tone']) }} & {{ ucfirst($botStats['personality_style']) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Chats</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($botStats['total_chats']) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Commands</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $botStats['commands_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Quick Replies</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $botStats['quick_replies_count'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                {{-- Messages Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Messages Over Time</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="messagesChart" width="400" height="200"></canvas>
                    </div>
                </div>

                {{-- AI Responses Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">AI Responses Over Time</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="aiResponsesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top Commands and Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Top Commands --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Commands (Last 30 Days)</h3>
                    @if(empty($topCommands))
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No command data available</p>
                    @else
                        <div class="space-y-3">
                            @foreach($topCommands as $command => $count)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $command }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $count }} uses</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                    @if(empty($recentActivity))
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No recent activity</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentActivity as $activity)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $activity['is_from_bot'] ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
                                        @if($activity['is_from_bot'])
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $activity['text'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['chat_username'] }} • {{ $activity['created_at'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Messages Chart
            const messagesCtx = document.getElementById('messagesChart');
            if (messagesCtx) {
                new Chart(messagesCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartData['messages']['labels'] ?? []),
                        datasets: [{
                            label: 'Messages',
                            data: @json($chartData['messages']['data'] ?? []),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // AI Responses Chart
            const aiResponsesCtx = document.getElementById('aiResponsesChart');
            if (aiResponsesCtx) {
                new Chart(aiResponsesCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartData['ai_responses']['labels'] ?? []),
                        datasets: [{
                            label: 'AI Responses',
                            data: @json($chartData['ai_responses']['data'] ?? []),
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</div>
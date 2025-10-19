<div class="min-h-screen bg-gradient-to-br from-[#FDFDFC] via-[#f8f7f4] to-[#FDFDFC] dark:from-[#0a0a0a] dark:via-[#1a1a1a] dark:to-[#0a0a0a]">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent">
                            Telegram Bot Settings
                        </h1>
                        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
                            Connect and configure your Telegram bot
                        </p>
                    </div>
                </div>
            </div>

            {{-- Status Card --}}
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Bot Status</h3>
                            <div class="flex items-center gap-2">
                                @if($is_active)
                                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Connected</p>
                                @else
                                    <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Not Connected</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($is_active)
                        <button wire:click="disconnectBot" 
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium"
                                onclick="return confirm('Are you sure you want to disconnect the bot?')">
                            Disconnect
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- Connection Settings --}}
                <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                    <h2 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Connection Settings</h2>
                    
                    <div class="space-y-6">
                        {{-- Bot Token --}}
                        <div>
                            <label class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                                Bot Token
                            </label>
                            <div class="flex gap-2">
                                <input wire:model="telegram_token" 
                                       type="password" 
                                       placeholder="Enter your bot token here" 
                                       class="flex-1 px-4 py-3 bg-gray-50 dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <button wire:click="testConnection" 
                                        class="px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                                    Test
                                </button>
                            </div>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                                Get your bot token from @BotFather on Telegram
                            </p>
                        </div>

                        {{-- Bot Info --}}
                        @if($bot_username || $bot_name)
                            <div class="bg-gray-50 dark:bg-[#1a1a1a] rounded-lg p-4">
                                <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Bot Information</h4>
                                @if($bot_name)
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        <span class="font-medium">Name:</span> {{ $bot_name }}
                                    </p>
                                @endif
                                @if($bot_username)
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        <span class="font-medium">Username:</span> @{{ $bot_username }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Connect Button --}}
                        <button wire:click="updateTelegramToken" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-semibold shadow-lg">
                            {{ $is_active ? 'Update Bot Token' : 'Connect Bot' }}
                        </button>
                    </div>
                </div>

                {{-- Webhook Information --}}
                <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                    <h2 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Webhook Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">
                                Webhook URL
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       value="{{ $webhook_url }}" 
                                       readonly 
                                       class="flex-1 px-4 py-3 bg-gray-50 dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-sm font-mono">
                                <button onclick="navigator.clipboard.writeText('{{ $webhook_url }}')" 
                                        class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium">
                                    Copy
                                </button>
                            </div>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                                This URL is automatically set when you connect your bot
                            </p>
                        </div>

                        {{-- Instructions --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Setup Instructions</h4>
                            <ol class="text-sm text-blue-700 dark:text-blue-300 space-y-1 list-decimal list-inside">
                                <li>Create a bot with @BotFather on Telegram</li>
                                <li>Copy the bot token provided by BotFather</li>
                                <li>Paste the token in the field above</li>
                                <li>Click "Test" to verify the connection</li>
                                <li>Click "Connect Bot" to activate</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="mt-8 bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                <h2 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Quick Actions</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('telegram-bot-builder') }}" 
                       class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <p class="font-medium">Customize Bot</p>
                            <p class="text-sm opacity-90">Personality & settings</p>
                        </div>
                    </a>

                    <a href="{{ route('telegram-analytics') }}" 
                       class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <div>
                            <p class="font-medium">View Analytics</p>
                            <p class="text-sm opacity-90">Usage & insights</p>
                        </div>
                    </a>

                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 p-4 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                        </svg>
                        <div>
                            <p class="font-medium">Back to Dashboard</p>
                            <p class="text-sm opacity-90">Main overview</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
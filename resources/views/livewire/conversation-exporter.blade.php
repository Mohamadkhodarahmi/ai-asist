<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Export Conversations</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Download your chat history in various formats for backup or analysis</p>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-800/50 text-red-800 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Export Stats --}}
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Total Conversations</p>
                        <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $exportStats['total_conversations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0c0-5 4-9 9-9s9 4 9 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Date Range</p>
                        <p class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                            @if(isset($exportStats['date_range']['from']) && $exportStats['date_range']['from'])
                                {{ \Carbon\Carbon::parse($exportStats['date_range']['from'])->format('M j') }} - 
                                {{ \Carbon\Carbon::parse($exportStats['date_range']['to'])->format('M j, Y') }}
                            @else
                                No conversations
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Available Formats</p>
                        <p class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">CSV, JSON, PDF</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Export Form --}}
        <div class="bg-white dark:bg-[#161615] rounded-xl p-8 border border-[#e3e3e0] dark:border-[#3E3E3A]">
            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Export Settings</h2>
            
            <form wire:submit="exportConversations">
                {{-- Format Selection --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-3">
                        Export Format *
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($formatOptions as $value => $label)
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model="format" value="{{ $value }}" 
                                       class="sr-only peer">
                                <div class="p-4 border-2 border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg peer-checked:border-[#F53003] peer-checked:bg-[#F53003]/5 hover:border-[#F53003]/50 transition-all">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $label }}</p>
                                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                                @if($value === 'csv')
                                                    Perfect for spreadsheet analysis
                                                @elseif($value === 'json')
                                                    Structured data for developers
                                                @else
                                                    Formatted document for reading
                                                @endif
                                            </p>
                                        </div>
                                        <div class="w-5 h-5 border-2 border-[#e3e3e0] dark:border-[#3E3E3A] rounded-full peer-checked:border-[#F53003] peer-checked:bg-[#F53003] flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('format') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Date Range --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-3">
                        Date Range (Optional)
                    </label>
                    
                    {{-- Quick Date Buttons --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" wire:click="setQuickDateRange('week')" 
                                class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            Last Week
                        </button>
                        <button type="button" wire:click="setQuickDateRange('month')" 
                                class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            Last Month
                        </button>
                        <button type="button" wire:click="setQuickDateRange('3months')" 
                                class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            Last 3 Months
                        </button>
                        <button type="button" wire:click="setQuickDateRange('year')" 
                                class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            Last Year
                        </button>
                        <button type="button" wire:click="setQuickDateRange('all')" 
                                class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            All Time
                        </button>
                    </div>

                    {{-- Custom Date Inputs --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">From Date</label>
                            <input type="date" wire:model="dateFrom" 
                                   class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#F53003] focus:border-transparent">
                            @error('dateFrom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">To Date</label>
                            <input type="date" wire:model="dateTo" 
                                   class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#F53003] focus:border-transparent">
                            @error('dateTo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Export Button --}}
                <div class="flex justify-end">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="px-8 py-3 bg-[#F53003] text-white rounded-xl font-semibold hover:bg-[#c41e00] transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg wire:loading wire:target="exportConversations" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="exportConversations" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span wire:loading.remove wire:target="exportConversations">Export Conversations</span>
                        <span wire:loading wire:target="exportConversations">Exporting...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Export Info --}}
        <div class="mt-8 bg-gradient-to-r from-[#F53003]/5 to-[#FF4433]/5 rounded-xl p-6 border border-[#F53003]/20">
            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-3">📄 About Conversation Export</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                <div>
                    <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">CSV Format:</h4>
                    <p>Comma-separated values perfect for Excel, Google Sheets, or data analysis. Includes timestamps, questions, answers, and metadata.</p>
                </div>
                <div>
                    <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">JSON Format:</h4>
                    <p>Structured data format ideal for developers and advanced users. Includes all conversation data with full metadata preservation.</p>
                </div>
                <div>
                    <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">PDF Format:</h4>
                    <p>Formatted HTML document for easy reading and sharing. Great for reports, documentation, or archival purposes.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for handling download --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('download-export', (event) => {
                window.location.href = event.url;
            });
        });
    </script>
</div>

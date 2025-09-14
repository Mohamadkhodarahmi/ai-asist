<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#161615] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-[#1b1b18] dark:text-[#EDEDEC]">

                    @if($business = auth()->user()->business)
                        {{-- USER HAS A BUSINESS, SHOW CHAT INTERFACE --}}
                        
                        <div class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] pb-6 mb-6">
                            <h1 class="text-2xl font-bold">Chat with: {{ $business->name }}</h1>
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">Ask questions based on the document you uploaded.</p>
                        </div>

                        {{-- Chat history window --}}
                        <div class="space-y-6 mb-8 h-96 overflow-y-auto pr-4" id="chat-window">
                            @foreach($history as $entry)
                                @if($entry['source'] === 'ai')
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#f53003]/10 text-[#f53003] flex items-center justify-center font-bold shrink-0">A</div>
                                        <div class="bg-[#e3e3e0]/40 dark:bg-[#3E3E3A]/40 p-3 rounded-lg rounded-tl-none max-w-md prose prose-sm dark:prose-invert">
                                            <p>{{ $entry['message'] }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start gap-3 justify-end">
                                        <div class="bg-[#f53003] text-white p-3 rounded-lg rounded-br-none max-w-md prose prose-sm prose-invert">
                                            <p>{{ $entry['message'] }}</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center font-bold shrink-0">Y</div>
                                    </div>
                                @endif
                            @endforeach

                            {{-- Livewire's built-in loading indicator. It automatically shows when a message is being sent. --}}
                            <div wire:loading.flex class="items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#f53003]/10 text-[#f53003] flex items-center justify-center font-bold shrink-0 animate-pulse">A</div>
                                <div class="bg-[#e3e3e0]/40 dark:bg-[#3E3E3A]/40 p-3 rounded-lg rounded-tl-none">
                                    <p class="typing-dots"><span>.</span><span>.</span><span>.</span></p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- This form now uses Livewire to handle submission. NO JAVASCRIPT NEEDED. --}}
                        <form wire:submit.prevent="sendMessage" class="relative">
                            <input wire:model="newMessage" type="text" placeholder="Type your message..." autocomplete="off" class="w-full pl-4 pr-24 py-3 bg-gray-50 dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-[#f53003] focus:border-transparent transition-all">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 py-2 px-4 bg-[#f53003] text-white rounded-md hover:bg-[#c41e00] transition-colors font-semibold shadow">Send</button>
                        </form>
                        
                        <hr class="my-8 border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">

                        {{-- Telegram Integration --}}
                        <div>
                           <h2 class="text-xl font-bold">Connect Telegram Bot</h2>
                           <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">Add your Telegram bot token to enable the assistant in your chat.</p>
                           <form action="{{ route('business.telegram.update') }}" method="POST" class="flex items-center gap-4">
                                @csrf
                                <input type="text" name="telegram_token" placeholder="Enter your bot token here" class="flex-grow px-4 py-2 bg-gray-50 dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded focus:outline-none focus:ring-2 focus:ring-[#f53003] transition-all" value="{{ $business->telegram_token ?? '' }}">
                                <button type="submit" class="py-2 px-5 bg-[#1b1b18] text-white rounded hover:bg-black transition-colors font-semibold shadow dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white">
                                    {{ $business->telegram_token ? 'Update' : 'Connect' }}
                                </button>
                            </form>
                        </div>

                    @else
                        {{-- This section for creating a new assistant remains the same --}}
                        <h1 class="text-2xl font-bold mb-2">Create Your First AI Assistant</h1>
                        <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">
                            To start chatting, you need to create an assistant. Give it a name and upload a document to serve as its knowledge base.
                        </p>
                        <form action="{{ route('business.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div>
                                <label for="business_name" class="block text-sm font-medium mb-1">Assistant Name</label>
                                <input id="business_name" type="text" name="name" required placeholder="e.g., 'Company Policy Bot'" class="w-full px-4 py-2 bg-gray-50 dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded focus:outline-none focus:ring-2 focus:ring-[#f53003] transition-all">
                            </div>
                            <div>
                                <label for="document" class="block text-sm font-medium mb-1">Knowledge Document</label>
                                <input id="document" type="file" name="document" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#f53003]/10 file:text-[#f53003] hover:file:bg-[#f53003]/20 dark:file:bg-[#FF4433]/10 dark:file:text-[#FF4433] dark:hover:file:bg-[#FF4433]/20">
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">Supported formats: PDF, TXT, DOCX.</p>
                            </div>
                            <button type="submit" class="w-full py-3 px-4 bg-[#f53003] text-white rounded-lg hover:bg-[#c41e00] transition-colors font-semibold shadow-lg">
                                Create Assistant and Start Chatting
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Add the typing animation style directly into the view --}}
    <style>
        .typing-dots span { animation: blink 1.4s infinite both; display: inline-block; }
        .typing-dots span:nth-child(2) { animation-delay: .2s; }
        .typing-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes blink { 0% { opacity: .2; } 20% { opacity: 1; } 100% { opacity: .2; } }
    </style>
</div>


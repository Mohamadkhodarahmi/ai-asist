<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#161615] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-[#1b1b18] dark:text-[#EDEDEC]">
                    
                    <h1 class="text-3xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-8">This is your central hub for managing your AI assistants.</p>

                    @if ($business = Auth::user()->business)
                        {{-- If user HAS a business/assistant --}}
                        <div class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6">
                            <h2 class="text-xl font-bold mb-1">Your Assistant: {{ $business->name }}</h2>
                            <div class="flex items-center gap-2 mb-6">
                                @if($business->telegram_token)
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    <p class="text-sm text-green-600 dark:text-green-400">Telegram Bot Connected</p>
                                @else
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-400">Telegram Bot Not Connected</p>
                                @endif
                            </div>

                            <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                                Your AI assistant is ready. You can start a conversation or manage its settings, including the Telegram bot integration.
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('chat') }}" class="w-full sm:w-auto text-center py-3 px-6 bg-[#f53003] text-white rounded-lg hover:bg-[#c41e00] transition-colors font-semibold shadow-lg">
                                    Go to Chat
                                </a>
                                <a href="{{ route('chat') }}" class="w-full sm:w-auto text-center py-3 px-6 bg-gray-100 dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] rounded-lg hover:bg-gray-200 dark:hover:bg-[#4a4a46] transition-colors font-semibold">
                                    Manage Assistant
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- If user does NOT have a business/assistant --}}
                        <div class="text-center border-2 border-dashed border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h2 class="mt-4 text-lg font-medium text-[#1b1b18] dark:text-white">You haven't created an assistant yet.</h2>
                            <p class="mt-1 text-[#706f6c] dark:text-[#A1A09A]">Get started by creating your first AI assistant from a document.</p>
                            <div class="mt-6">
                                <a href="{{ route('chat') }}" class="inline-block py-2 px-5 bg-[#f53003] text-white rounded-lg hover:bg-[#c41e00] transition-colors font-semibold shadow">
                                    Create Your First Assistant
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

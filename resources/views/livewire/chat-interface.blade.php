<div class="w-full max-w-lg mx-auto p-6 bg-white dark:bg-[#161615] rounded-lg shadow-lg animate-fade-in flex flex-col gap-6">
    <div class="chat-window h-80 overflow-y-auto border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 rounded bg-[#FDFDFC] dark:bg-[#0a0a0a] transition-all">
        @foreach ($history as $chat)
            <div class="mb-4" wire:key="chat-{{ $loop->index }}">
                <span class="font-semibold text-[#f53003] dark:text-[#FF4433]">{{ ucfirst($chat['source']) }}:</span>
                <p class="ml-2 text-[#1b1b18] dark:text-[#EDEDEC]">{{ $chat['message'] }}</p>
            </div>
        @endforeach
    </div>
    <form wire:submit.prevent="sendMessage" class="flex gap-2">
        <input type="text" wire:model.live="newMessage" placeholder="Ask something..." class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#f53003] transition-all bg-[#FDFDFC] dark:bg-[#161615] dark:text-[#EDEDEC]" autocomplete="off">
        <button type="submit" class="px-4 py-2 bg-[#f53003] text-white rounded hover:bg-[#c41e00] transition-colors font-semibold shadow">Send</button>
    </form>
</div>

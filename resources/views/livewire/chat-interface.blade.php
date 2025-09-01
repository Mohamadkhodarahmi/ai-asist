<div> {{-- root div required by Livewire --}}
    <div class="chat-window" style="height: 400px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; margin: 2rem;">
        @forelse ($history as $chat)
            <div class="message" style="margin-bottom: 10px;">
                <strong>{{ ucfirst($chat['source']) }}:</strong>
                <p style="margin: 0;">{{ $chat['message'] }}</p>
            </div>
        @empty
            <div style="text-align: center; color: #888;">
                The chat history is empty. Ask a question to begin!
            </div>
        @endforelse
    </div>

    <form wire:submit.prevent="sendMessage" style="display: flex; padding: 2rem;">
        <input type="text" wire:model="newMessage" placeholder="Ask something..." style="flex-grow: 1; padding: 0.5rem;">
        <button type="submit" style="padding: 0.5rem 1rem; margin-left: 0.5rem;">Send</button>
    </form>
</div>

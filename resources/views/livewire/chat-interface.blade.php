<div>
    {{-- This is a very basic chat UI. You can style it with CSS. --}}
    <div class="chat-window" style="height: 400px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px;">
        @foreach ($history as $chat)
            <div class="message" style="margin-bottom: 10px;">
                <strong>{{ ucfirst($chat['source']) }}:</strong>
                <p>{{ $chat['message'] }}</p>
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage">
        <input type="text" wire:model="newMessage" placeholder="Ask something..." style="width: 80%;">
        <button type="submit">Send</button>
    </form>
</div>

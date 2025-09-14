<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.app-layout')]
class ChatInterface extends Component
{
    public string $newMessage = '';
    public array $history = [];
    public ?int $businessId;
    public bool $loading = false;

    public function mount()
    {
        $this->businessId = Auth::user()->business?->id;
        // Initialize with a welcome message if the history is empty
        if (empty($this->history)) {
            $this->history[] = ['source' => 'ai', 'message' => 'Hello! How can I help you today based on your document?'];
        }
    }

    public function sendMessage(ChatService $chatService)
    {
        if (is_null($this->businessId) || empty(trim($this->newMessage))) {
            return;
        }

        // Add user's message to history and store the question
        $question = $this->newMessage;
        $this->history[] = ['source' => 'user', 'message' => $question];
        
        // Clear the input field immediately and set the loading state
        $this->newMessage = ''; 
        $this->loading = true;

        // Call the service to get the AI's answer
        $response = $chatService->getAnswer($question, $this->businessId);

        // Add AI's response to the history and turn off the loading state
        $this->history[] = ['source' => 'ai', 'message' => $response];
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}


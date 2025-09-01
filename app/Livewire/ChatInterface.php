<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth; // Import the Auth facade

class ChatInterface extends Component
{
    public string $newMessage = '';
    public array $history = [];
    public ?int $businessId; // Make businessId nullable

    /**
     * The mount method is like a constructor for Livewire components.
     * It runs when the component is first loaded.
     */
    public function mount()
    {
        // Get the business ID from the currently authenticated user.
        $this->businessId = Auth::user()->business_id;
    }

    public function sendMessage(ChatService $chatService)
    {
        // First, check if the user is actually part of a business.
        if (is_null($this->businessId)) {
            $this->history[] = ['source' => 'ai', 'message' => 'Error: Your user account is not linked to a business.'];
            return;
        }

        // Add user's message to the history
        $this->history[] = ['source' => 'user', 'message' => $this->newMessage];

        // Call the backend service with the correct business ID
        $response = $chatService->getAnswer($this->newMessage, $this->businessId);

        // Add AI's response to the history
        $this->history[] = ['source' => 'ai', 'message' => $response];

        // Clear the input box
        $this->newMessage = '';
    }

    public function render()
    {
        return view('livewire.chat-interface')->layout('layouts.app');
    }
}

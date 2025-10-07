<?php

namespace App\Livewire;

use App\Services\AnalyticsService;
use App\Services\ChatService;
use App\Services\PlanLimiter;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

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

    public function sendMessage(ChatService $chatService, PlanLimiter $planLimiter, AnalyticsService $analyticsService)
    {
        if (is_null($this->businessId) || empty(trim($this->newMessage))) {
            return;
        }

        $user = Auth::user();
        if (! $planLimiter->canSendMessage($user)) {
            $this->dispatch('toast', type: 'error', message: 'Daily message limit reached for your plan.');

            return;
        }

        // Add user's message to history and store the question
        $question = $this->newMessage;
        $this->history[] = ['source' => 'user', 'message' => $question];

        // Clear the input field immediately and set the loading state
        $this->newMessage = '';
        $this->loading = true;

        // Track the start time for response time measurement
        $startTime = microtime(true);

        // Call the service to get the AI's answer
        $response = $chatService->getAnswer($question, $this->businessId);

        // Calculate response time
        $responseTime = round((microtime(true) - $startTime) * 1000); // Convert to milliseconds

        // Track analytics
        $analyticsService->trackChatInteraction(
            $user,
            $this->businessId,
            $question,
            $response,
            $responseTime,
            null, // tokens_used - would need to be provided by ChatService
            ['interface' => 'web']
        );

        // Track user activity
        $analyticsService->trackUserActivity($user, 'chat_interaction', [
            'question_length' => strlen($question),
            'response_length' => strlen($response),
            'response_time_ms' => $responseTime,
        ]);

        // Count towards plan usage only if we attempted a message
        $planLimiter->hit($user);

        // Add AI's response to the history and turn off the loading state
        $this->history[] = ['source' => 'ai', 'message' => $response];
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}

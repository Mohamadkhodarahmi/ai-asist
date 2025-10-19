<?php

namespace App\Livewire;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class TelegramSettings extends Component
{
    public string $telegram_token = '';

    public string $bot_username = '';

    public string $bot_name = '';

    public bool $is_active = false;

    public string $webhook_url = '';

    public function mount()
    {
        $business = Auth::user()->business;

        if ($business) {
            $this->telegram_token = $business->telegram_token ?? '';
            $this->bot_username = $business->bot_username ?? '';
            $this->bot_name = $business->name ?? '';
            $this->is_active = $business->telegram_token ? true : false;
            $this->webhook_url = $this->generateWebhookUrl();
        }
    }

    public function updateTelegramToken()
    {
        $this->validate([
            'telegram_token' => [
                'required',
                'string',
                'regex:/^[0-9]{8,10}:[a-zA-Z0-9_-]{35}$/',
            ],
        ], [
            'telegram_token.regex' => 'The token format is invalid.',
        ]);

        $business = Auth::user()->business;

        if (! $business) {
            session()->flash('error', 'You must create an assistant first.');

            return;
        }

        try {
            // Update the business with the new token
            $business->update(['telegram_token' => $this->telegram_token]);

            // Dispatch job to set webhook
            \App\Jobs\SetTelegramWebhook::dispatch($business);

            $this->is_active = true;
            $this->webhook_url = $this->generateWebhookUrl();

            session()->flash('success', 'Telegram bot connected successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to connect Telegram bot: '.$e->getMessage());
        }
    }

    public function disconnectBot()
    {
        $business = Auth::user()->business;

        if ($business) {
            $business->update(['telegram_token' => null]);
            $this->telegram_token = '';
            $this->is_active = false;
            $this->webhook_url = '';

            session()->flash('success', 'Telegram bot disconnected successfully!');
        }
    }

    public function testConnection()
    {
        if (! $this->telegram_token) {
            session()->flash('error', 'Please enter a bot token first.');

            return;
        }

        try {
            $response = \Http::get("https://api.telegram.org/bot{$this->telegram_token}/getMe");

            if ($response->successful()) {
                $botInfo = $response->json()['result'];
                $this->bot_username = $botInfo['username'] ?? '';
                $this->bot_name = $botInfo['first_name'] ?? '';

                session()->flash('success', 'Bot connection successful! Bot: @'.$this->bot_username);
            } else {
                session()->flash('error', 'Failed to connect to bot. Please check your token.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Connection test failed: '.$e->getMessage());
        }
    }

    private function generateWebhookUrl(): string
    {
        if (! $this->telegram_token) {
            return '';
        }

        $token = explode(':', $this->telegram_token)[0];

        return route('telegram.webhook', ['token' => $token], true);
    }

    public function render()
    {
        return view('livewire.telegram-settings');
    }
}

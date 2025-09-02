<?php

namespace App\Services;

use App\Models\TelegramBot;
use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramWebhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $apiBaseUrl = 'https://api.telegram.org/bot';

    public function __construct(
        private ChatService $chatService,
        private TelegramAnalyticsService $analyticsService
    ) {}

    public function registerWebhook(TelegramBot $bot): bool
    {
        try {
            $response = Http::post($this->apiBaseUrl . $bot->bot_token . '/setWebhook', [
                'url' => $bot->webhook_url,
                'allowed_updates' => ['message', 'callback_query'],
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Failed to set webhook', [
                'bot_id' => $bot->id,
                'response' => $response->json()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception setting webhook', [
                'bot_id' => $bot->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendMessage(TelegramChat $chat, string $message): ?TelegramMessage
    {
        try {
            $response = Http::post($this->apiBaseUrl . $chat->bot->bot_token . '/sendMessage', [
                'chat_id' => $chat->chat_id,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                $messageData = $response->json()['result'];
                
                return TelegramMessage::create([
                    'telegram_chat_id' => $chat->id,
                    'message_id' => $messageData['message_id'],
                    'message_text' => $message,
                    'is_from_bot' => true,
                    'telegram_timestamp' => now(),
                ]);
            }

            Log::error('Failed to send message', [
                'chat_id' => $chat->id,
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception sending message', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function handleUpdate(array $update): void
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    private function handleMessage(array $message): void
    {
        $start = microtime(true);
        $isSuccessful = true;
        $isAiProcessed = false;

        try {
            $botToken = request()->route('token');
            $bot = TelegramBot::where('bot_token', $botToken)
                ->where('is_active', true)
                ->firstOrFail();

            // Find or create chat
            $chat = TelegramChat::firstOrCreate(
                ['chat_id' => $message['chat']['id'], 'telegram_bot_id' => $bot->id],
                [
                    'chat_type' => $message['chat']['type'],
                    'username' => $message['chat']['username'] ?? null,
                    'first_name' => $message['chat']['first_name'] ?? null,
                    'last_name' => $message['chat']['last_name'] ?? null,
                ]
            );

            // Store the incoming message
            $telegramMessage = TelegramMessage::create([
                'telegram_chat_id' => $chat->id,
                'message_id' => $message['message_id'],
                'message_text' => $message['text'] ?? '',
                'is_from_bot' => false,
                'telegram_timestamp' => now(),
            ]);

            // Process message with AI if it contains text
            if (isset($message['text']) && $bot->is_active) {
                try {
                    $isAiProcessed = true;
                    
                    $aiResponse = $this->chatService->getAnswer(
                        $message['text'],
                        $bot->business_id
                    );

                    if ($aiResponse) {
                        $this->sendMessage($chat, $aiResponse);
                    }
                } catch (\Exception $e) {
                    $isSuccessful = false;
                    Log::error('AI processing failed', [
                        'error' => $e->getMessage(),
                        'chat_id' => $chat->id,
                        'message' => $message
                    ]);

                    $this->sendMessage($chat, 'I apologize, but I encountered an error processing your message. Please try again later.');
                }
            }

            // Track analytics
            $responseTime = microtime(true) - $start;
            $this->analyticsService->trackMessage($bot, $isAiProcessed, $responseTime, $isSuccessful);
            $this->analyticsService->updateActiveUsers($bot);

        } catch (\Exception $e) {
            Log::error('Message handling failed', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
            $isSuccessful = false;
        }
    }

    private function getChatContext(TelegramChat $chat): array
    {
        // Get last 5 messages for context
        return $chat->messages()
            ->orderBy('telegram_timestamp', 'desc')
            ->take(5)
            ->get()
            ->map(function ($message) {
                return [
                    'role' => $message->is_from_bot ? 'assistant' : 'user',
                    'content' => $message->message_text
                ];
            })
            ->reverse()
            ->values()
            ->toArray();
    }

    private function handleCallbackQuery(array $callbackQuery): void
    {
        // Store the webhook data for debugging
        TelegramWebhook::create([
            'telegram_bot_id' => TelegramBot::where('bot_token', request()->route('token'))->value('id'),
            'payload' => $callbackQuery,
            'event_type' => 'callback_query',
            'is_processed' => true
        ]);
    }
}
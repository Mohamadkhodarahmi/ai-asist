<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\TelegramBot;
use App\Services\EnhancedTelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Business $business;

    protected int $chatId;

    protected string $messageText;

    protected int $messageId;

    protected ?string $firstName;

    protected ?string $lastName;

    protected ?string $username;

    public function __construct(Business $business, int $chatId, string $messageText, int $messageId, ?string $firstName, ?string $lastName, ?string $username)
    {
        $this->business = $business;
        $this->chatId = $chatId;
        $this->messageText = $messageText;
        $this->messageId = $messageId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
    }

    public function handle(EnhancedTelegramService $enhancedTelegramService): void
    {
        try {
            // Find the Telegram bot for this business
            $bot = TelegramBot::where('business_id', $this->business->id)->first();
            
            if (!$bot) {
                Log::warning('No Telegram bot found for business', [
                    'business_id' => $this->business->id,
                ]);
                return;
            }

            // Process the message using the enhanced service
            $messageData = [
                'chat' => [
                    'id' => $this->chatId,
                    'type' => 'private',
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'username' => $this->username,
                ],
                'message_id' => $this->messageId,
                'text' => $this->messageText,
            ];

            $enhancedTelegramService->processMessage($bot, $messageData);

            Log::info('Telegram message processed successfully', [
                'business_id' => $this->business->id,
                'bot_id' => $bot->id,
                'chat_id' => $this->chatId,
                'question' => $this->messageText,
            ]);

        } catch (Throwable $e) {
            Log::error('Failed to process Telegram message', [
                'business_id' => $this->business->id,
                'chat_id' => $this->chatId,
                'question' => $this->messageText,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

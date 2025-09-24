<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\TelegramChat;
use App\Services\ChatService;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
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

    public function handle(ChatService $chatService): void
    {
        $answer = null;

        try {
            // Get AI answer
            $answer = $chatService->getAnswer($this->messageText, $this->business->id);

            // Send it via Telegram
            $telegramService = new TelegramService($this->business->telegram_token);
            $telegramService->sendMessage($this->chatId, $answer);

            Log::info('Telegram message processed', [
                'business_id' => $this->business->id,
                'chat_id' => $this->chatId,
                'question' => $this->messageText,
                'answer' => $answer,
            ]);

        } catch (Throwable $e) {
            Log::error('Failed to process Telegram message', [
                'business_id' => $this->business->id,
                'chat_id' => $this->chatId,
                'question' => $this->messageText,
                'error' => $e->getMessage(),
            ]);
        }
        $chat = TelegramChat::firstOrCreate(
            ['chat_id' => $this->chatId], // Search by the Telegram chat_id
            [                             // Data to use if creating a new record
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'username' => $this->username,
                'type' => 'private',
                // 'telegram_bot_id' => $this->business->id, // You may need to pass the business ID to the job for this
            ]
        );
        // Save incoming message and AI response to the DB
        DB::table('telegram_messages')->insert([
            'telegram_chat_id' => $chat->id,
            'message_id' => $this->messageId, // optionally store Telegram message_id if you pass it
            'message_text' => $this->messageText,
            'is_from_bot' => false,
            'telegram_timestamp' => now(), // or use actual Telegram timestamp if available
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($answer) {
            DB::table('telegram_messages')->insert([
                'telegram_chat_id' => $chat->id,
                'message_id' => null,
                'message_text' => $answer,
                'is_from_bot' => true,
                'telegram_timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

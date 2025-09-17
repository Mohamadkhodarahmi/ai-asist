<?php

namespace App\Jobs;

use App\Models\Business;
use App\Services\ChatService;
use App\Services\TelegramService;
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

    /**
     * Create a new job instance.
     */
    public function __construct(Business $business, int $chatId, string $messageText)
    {
        $this->business = $business;
        $this->chatId = $chatId;
        $this->messageText = $messageText;
    }

    /**
     * Execute the job.
     */
    public function handle(ChatService $chatService): void
    {
        try {
            // 1. Get the AI's answer using the existing ChatService.
            $answer = $chatService->getAnswer($this->messageText, $this->business->id);

            // 2. Initialize the Telegram service with the business's token.
            $telegramService = new TelegramService($this->business->telegram_token);

            // 3. Send the answer back to the user on Telegram.
            $telegramService->sendMessage($this->chatId, $answer);

        } catch (Throwable $e) {
            Log::error('Failed to process Telegram message: ' . $e->getMessage(), [
                'business_id' => $this->business->id,
                'chat_id' => $this->chatId,
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TelegramWebhook;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private TelegramService $telegramService
    ) {}

    public function handle(Request $request)
    {
        try {
            // Bot is already validated by middleware and available in request
            $bot = $request->telegram_bot;

            // Log the webhook
            $webhook = TelegramWebhook::create([
                'telegram_bot_id' => $bot->id,
                'payload' => $request->all(),
                'event_type' => $this->determineEventType($request->all()),
                'is_processed' => false,
            ]);

            // Process the update
            $this->telegramService->handleUpdate($request->all());

            // Mark webhook as processed
            $webhook->update([
                'is_processed' => true
            ]);

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Webhook handling failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    private function determineEventType(array $update): string
    {
        if (isset($update['message'])) {
            return 'message';
        } elseif (isset($update['callback_query'])) {
            return 'callback_query';
        }
        return 'unknown';
    }
}
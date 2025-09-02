<?php

namespace App\Http\Controllers;

use App\Models\TelegramBot;
use App\Models\TelegramWebhook;
use App\Services\TelegramService;
use App\Http\Resources\TelegramMessageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            $result = $this->telegramService->handleUpdate($request->all());

            // Mark webhook as processed
            $webhook->update([
                'is_processed' => true
            ]);

            if ($result && $result instanceof TelegramMessageResource) {
                return $result;
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process webhook'
            ], 500);
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
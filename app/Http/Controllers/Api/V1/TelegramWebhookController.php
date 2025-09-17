<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTelegramMessage;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Handle incoming updates from the Telegram webhook.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $token The bot token from the URL.
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, string $token): JsonResponse
    {
        // 1. Find the business associated with this token.
        $business = Business::where('telegram_token', $token)->first();

        // If no business is found, or the update is invalid, ignore it.
        if (!$business || !$request->has('message.text') || !$request->has('message.chat.id')) {
            Log::warning('Invalid webhook call', ['token' => $token, 'data' => $request->all()]);
            return response()->json(['status' => 'error', 'message' => 'Invalid request'], 404);
        }

        // 2. Extract essential data.
        $chatId = $request->input('message.chat.id');
        $messageText = $request->input('message.text');

        // 3. Dispatch a job to handle the processing in the background.
        ProcessTelegramMessage::dispatch($business, $chatId, $messageText);

        // 4. Immediately return a 200 OK to Telegram to acknowledge receipt.
        return response()->json(['status' => 'ok']);
    }
}
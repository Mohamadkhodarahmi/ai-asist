<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTelegramMessage;
use App\Models\Business;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    /**
     * Handle incoming Telegram bot updates (webhook).
     *
     * This method receives updates from the Telegram servers for a specific bot.
     * It finds the associated business, processes the user's message using the
     * ChatService, and sends the answer back.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request from Telegram.
     * @param  string  $token  The unique bot token from the webhook URL.
     * @param  \App\Services\ChatService  $chatService  The service to generate AI answers.
     */
    public function handle(Request $request, string $token, ChatService $chatService): JsonResponse
    {
        Log::info('Incoming Telegram webhook', [
            'token' => $token,
            'payload' => $request->all(),
        ]);

        $business = Business::where('telegram_token', $token)->firstOrFail();
        $message = $request->input('message');

        if (! $message || ! isset($message['chat']['id']) || ! isset($message['text'])) {
            Log::warning('Unsupported Telegram update', ['payload' => $request->all()]);

            return response()->json(['status' => 'ignored']);
        }

        $chatId = $message['chat']['id'];
        $text = $message['text'];
        $messageId = $message['message_id'];

        $firstName = $chat['first_name'] ?? null;
        $lastName = $chat['last_name'] ?? null;
        $username = $chat['username'] ?? null;

        // Dispatch the job for async processing and DB logging
        ProcessTelegramMessage::dispatch(
            $business,
            $chatId,
            $text,
            $messageId,
            $firstName,
            $lastName,
            $username
        );

        return response()->json(['status' => 'ok']);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessKnowledgeFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Throwable;
use App\Models\Business;
use App\Services\TelegramService; // Import the new service
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    /**
     * Store a new business (AI Assistant) for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ... (your existing store method remains the same)
    }

    /**
     * Update the Telegram bot token and set the webhook.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTelegram(Request $request): RedirectResponse
    {
        $request->validate([
            'telegram_token' => ['required', 'string', 'regex:/^[0-9]{8,10}:[a-zA-Z0-9_-]{35}$/'],
        ], [
            'telegram_token.regex' => 'The token format is invalid.'
        ]);

        $business = $request->user()->business;

        if (!$business) {
            return redirect()->route('chat')->withErrors(['telegram' => 'You must create an assistant first.']);
        }

        $token = $request->telegram_token;

        try {
            // 1. Construct the webhook URL. The token is included for security.
            $webhookUrl = route('telegram.webhook', ['token' => $token]);

            // 2. Use the service to set the webhook with Telegram.
            $telegramService = new TelegramService($token);
            $response = $telegramService->setWebhook($webhookUrl);

            // 3. Check if Telegram accepted the webhook.
            if (!$response->ok() || !$response->json('ok')) {
                throw new \Exception('Telegram API error: ' . $response->json('description', 'Could not set webhook.'));
            }

            // 4. If successful, save the token to the database.
            $business->update(['telegram_token' => $token]);

        } catch (Throwable $e) {
            Log::error('Telegram webhook setup failed: ' . $e->getMessage());
            return back()->withErrors(['telegram_token' => 'Connection failed. Please check your token and try again.']);
        }

        return redirect()->route('chat')->with('status', 'Telegram bot connected successfully!');
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TelegramBot;
use Symfony\Component\HttpFoundation\Response;

class ValidateTelegramWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');
        
        // Verify bot exists and is active
        $bot = TelegramBot::where('bot_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid bot token or bot is inactive'], 403);
        }

        // Store bot in request for later use
        $request->merge(['telegram_bot' => $bot]);

        return $next($request);
    }
}
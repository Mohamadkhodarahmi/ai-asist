<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateTelegramWebhook
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Basic structure validation for Telegram updates
        if (! $request->isJson()) {
            return response()->json(['message' => 'Invalid payload. Expected JSON.'], 400);
        }

        $payload = $request->json()->all();

        if (! array_key_exists('update_id', $payload)) {
            return response()->json(['message' => 'Invalid Telegram update: missing update_id.'], 400);
        }

        // Optional: verify Telegram secret token header if configured
        $expectedSecret = (string) (config('services.telegram.webhook_secret') ?? '');
        if ($expectedSecret !== '') {
            $providedSecret = (string) ($request->header('X-Telegram-Bot-Api-Secret-Token') ?? '');
            if (! hash_equals($expectedSecret, $providedSecret)) {
                return response()->json(['message' => 'Forbidden. Invalid Telegram secret token.'], 403);
            }
        }

        return $next($request);
    }
}

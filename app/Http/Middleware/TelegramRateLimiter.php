<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter as CacheRateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramRateLimiter
{
    public function __construct(private CacheRateLimiter $limiter) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Key on bot token + chat id when possible; fallback to IP.
        $botToken = (string) ($request->route('token') ?? '');
        $chatId = (string) (data_get($request->json()->all(), 'message.chat.id') ?? data_get($request->json()->all(), 'callback_query.message.chat.id') ?? '');

        $keyParts = array_filter(['telegram', $botToken, $chatId ?: $request->ip()]);
        $key = Str::slug(implode(':', $keyParts));

        // Allow 30 requests per 30 seconds (tunable)
        $maxAttempts = 30;
        $decaySeconds = 30;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);

            return response()->json([
                'message' => 'Too Many Requests',
                'retry_after' => $retryAfter,
            ], 429);
        }

        $this->limiter->hit($key, $decaySeconds);

        return $next($request);
    }
}

<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('telegram', function (Request $request) {
            $botToken = (string) ($request->route('token') ?? '');
            $chatId = (string) (data_get($request->json()->all(), 'message.chat.id') ?? data_get($request->json()->all(), 'callback_query.message.chat.id') ?? '');

            $key = 'telegram:'.md5($botToken.'|'.($chatId ?: $request->ip()));

            return [
                Limit::perMinute(60)->by($key),
            ];
        });
    }
}

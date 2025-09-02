<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class RateLimiterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('telegram', function (Request $request) {
            $key = 'telegram:' . $request->route('token');
            Log::debug('Rate limiting configuration for telegram', [
                'key' => $key,
                'attempts' => RateLimiter::attempts($key),
                'remaining' => RateLimiter::remaining($key, 10),
                'rate_limit_key' => RateLimiter::key($key)
            ]);
            
            return Limit::perMinute(10)->by($key);
        });
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class TelegramRateLimiter
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'telegram:' . $request->route('token');
        
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Please wait before sending more requests.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60);
        return $next($request);
    }
}
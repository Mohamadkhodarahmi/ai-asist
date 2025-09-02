<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\KnowledgeFileController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\TelegramBotController;
use App\Http\Controllers\Api\V1\TelegramWebhookController;
use App\Http\Controllers\Api\V1\TelegramAnalyticsController;

// This route returns the authenticated user's information
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Group all our version 1 API routes under a protected middleware
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Knowledge File Routes
    Route::apiResource('knowledge-files', KnowledgeFileController::class);
    Route::post('/chat/ask', [ChatController::class, 'ask']);

    // Telegram Bot Routes
    Route::apiResource('telegram-bots', TelegramBotController::class);
    Route::post('telegram-bots/{telegram_bot}/toggle', [TelegramBotController::class, 'toggleActivation']);
    Route::get('telegram-bots/{telegram_bot}/chats/{chat}/history', [TelegramBotController::class, 'chatHistory']);
    
    // Telegram Analytics Routes
    Route::get('telegram-bots/{telegram_bot}/analytics', [TelegramAnalyticsController::class, 'getMetrics']);
    Route::get('telegram/dashboard-stats', [TelegramAnalyticsController::class, 'getDashboardStats']);
});

// Telegram Webhook Route (Public, but protected by validation and rate limiting)
Route::middleware('telegram.webhook')->group(function () {
    Route::middleware(['throttle:10,1,telegram'])->group(function () {
        Route::post('/webhook/telegram/{token}', [TelegramWebhookController::class, 'handle'])
            ->name('telegram.webhook');
    });
});

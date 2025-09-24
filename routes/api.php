<?php

use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\KnowledgeFileController;
use App\Http\Controllers\Api\V1\TelegramBotController;
use App\Http\Controllers\Api\V1\TelegramWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// Authenticated user info
// Test endpoint
Route::post('/v1/telegram/webhook/test', function (Request $request) {
    Log::info('Telegram webhook test hit', $request->all());

    return response()->json(['status' => 'ok']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Telegram webhook endpoints
// Route::post('/v1/telegram/webhook/{token}', [TelegramWebhookController::class, 'handle'])
//     ->name('telegram.webhook');

Route::post('/v1/telegram/bot/{token}', [TelegramBotController::class, 'handle'])
    ->name('telegram.bot');

// API v1 group (protected)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Knowledge File routes
    Route::apiResource('knowledge-files', KnowledgeFileController::class);

    // Chat route
    Route::post('/chat/ask', [ChatController::class, 'ask']);
});

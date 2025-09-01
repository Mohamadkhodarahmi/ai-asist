<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\KnowledgeFileController;
use App\Http\Controllers\Api\V1\ChatController;

// This route returns the authenticated user's information.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Group all our version 1 API routes under a protected middleware.
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // --- Knowledge File Routes ---
    // GET /api/v1/knowledge-files -> Lists all files for the business.
    // POST /api/v1/knowledge-files -> Uploads a new file.
    // GET /api/v1/knowledge-files/{knowledgeFile} -> Shows a specific file's status.
    // DELETE /api/v1/knowledge-files/{knowledgeFile} -> Deletes a file.
    Route::apiResource('knowledge-files', KnowledgeFileController::class);

    // --- Chat Route ---
    // POST /api/v1/chat/ask -> Asks a question to the AI.
    Route::post('/chat/ask', [ChatController::class, 'ask']);
});

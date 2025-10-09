<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API v1 routes with authentication middleware
Route::prefix('v1')->middleware(['api.auth'])->group(function () {
    // Test endpoint
    Route::get('/test', [ApiController::class, 'test']);
    
    // Chat endpoint
    Route::post('/chat', [ApiController::class, 'chat']);
    
    // Document endpoints
    Route::get('/documents', [ApiController::class, 'getDocuments']);
    Route::post('/search', [ApiController::class, 'searchDocuments']);
    
    // Usage and rate limit endpoints
    Route::get('/usage', [ApiController::class, 'getUsageStats']);
    Route::get('/rate-limits', [ApiController::class, 'getRateLimits']);
});
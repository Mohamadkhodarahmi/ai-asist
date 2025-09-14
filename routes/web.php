<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
// No longer need ChatPageController for this route
use App\Http\Controllers\Api\V1\BusinessController;
use App\Http\Controllers\Api\V1\ChatController as ApiChatController;
use App\Livewire\ChatInterface; // Import the Livewire component
use App\Livewire\FileUpload;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Home Page ---
Route::get('/', function () {
    return view('welcome');
})->name('home');


// --- Guest-Only Routes ---
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});


// --- Authenticated User Routes ---
Route::middleware('auth')->group(function () {
    // General Authenticated Routes
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Chat & AI Assistant (Business) Routes
    // MODIFIED: Route now points directly to the Livewire component
    Route::get('/chat', ChatInterface::class)->name('chat');
    Route::post('/business', [BusinessController::class, 'store'])->name('business.store');
    Route::post('/business/telegram', [BusinessController::class, 'updateTelegram'])->name('business.telegram.update');

    Route::get('/upload', FileUpload::class)->name('upload');
});


// --- Authenticated API Routes ---
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/chat/ask', [ApiChatController::class, 'ask'])->name('api.chat.ask');
});


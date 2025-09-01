<?php

use Illuminate\Support\Facades\Route;
// routes/web.php
use App\Livewire\ChatInterface;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Livewire\FileUpload;

// --- Guest Routes ---
// Routes that are only accessible when the user is not logged in.
Route::middleware('guest')->group(function () {
    // Registration routes
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// --- Authenticated Routes ---
// Routes that require a user to be logged in.
Route::middleware('auth')->group(function () {
    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Your dashboard or other protected routes
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::middleware('auth:sanctum')->group(function () {
    // ... other authenticated routes
    Route::post('/v1/chat/ask', [ChatController::class, 'ask']);
});

Route::get('/chat', ChatInterface::class);
Route::get('/upload', FileUpload::class)->middleware('auth');

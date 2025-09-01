<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\ChatInterface;
use App\Livewire\FileUpload;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// --- Public Welcome Page ---
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// --- Guest Routes ---
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // MOVED: The upload and chat pages now require a user to be logged in.
    Route::get('/upload', FileUpload::class)->name('upload');
    Route::get('/chat', ChatInterface::class)->name('chat');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

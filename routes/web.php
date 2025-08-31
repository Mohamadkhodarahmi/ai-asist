<?php

use Illuminate\Support\Facades\Route;
// routes/web.php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

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

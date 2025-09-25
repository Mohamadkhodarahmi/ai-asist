<?php

use App\Http\Controllers\Api\V1\BusinessController;
use App\Http\Controllers\Api\V1\ChatController as ApiChatController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
// No longer need ChatPageController for this route
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Livewire\ChatInterface;
use App\Livewire\FileUpload; // Import the Livewire component
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Home Page ---
Route::get('/', function () {
    return view('welcome');
})->name('home');

// --- Public Static Pages ---
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/docs', 'docs')->name('docs');
Route::view('/blog', 'blog')->name('blog');
Route::view('/community', 'community')->name('community');
Route::view('/about', 'about')->name('about');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/contact', 'contact')->name('contact');

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
    Route::post('/plan/select', [PlanController::class, 'select'])->name('plan.select');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::post('/business', [BusinessController::class, 'store'])->name('business.store');
    Route::post('/business/telegram', [BusinessController::class, 'updateTelegram'])->name('business.telegram.update');

    Route::get('/upload', FileUpload::class)->name('upload');
});

// --- Authenticated API Routes ---
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/chat/ask', [ApiChatController::class, 'ask'])->name('api.chat.ask');
});

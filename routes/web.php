<?php

use App\Http\Controllers\Api\V1\BusinessController;
use App\Http\Controllers\Api\V1\ChatController as ApiChatController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ExportController;
// No longer need ChatPageController for this route
use App\Http\Controllers\PlanController;
use App\Livewire\ChatInterface;
use App\Livewire\FileUpload; // Import the Livewire component
use App\Livewire\TelegramBotBuilder;
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

// --- Email Verification Routes ---
use App\Http\Controllers\Auth\EmailVerificationController;

// Email verification notice (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');

    // Resend verification email (requires auth)
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware(['throttle:6,1'])->name('verification.send');

    // Email correction routes
    Route::get('/email/correct', [EmailVerificationController::class, 'showEmailCorrection'])->name('verification.correct-email');
    Route::post('/email/update', [EmailVerificationController::class, 'updateEmail'])->name('verification.update-email');
    
    // Account deletion routes
    Route::get('/email/delete-account', [EmailVerificationController::class, 'showDeleteAccount'])->name('verification.delete-account.show');
    Route::post('/email/delete-account', [EmailVerificationController::class, 'deleteAccount'])->name('verification.delete-account');
});

// Email verification handler (NO auth required - user will be logged in after verification)
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed'])->name('verification.verify');

// --- Authenticated User Routes ---
Route::middleware(['auth', 'verified'])->group(function () {
    // General Authenticated Routes (requires email verification)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/pricing', [PlanController::class, 'index'])->name('pricing');
    Route::get('/analytics', \App\Livewire\AnalyticsDashboard::class)->name('analytics');
    Route::get('/personality', \App\Livewire\PersonalityManager::class)->name('personality');
    Route::get('/export', \App\Livewire\ConversationExporter::class)->name('export');
    Route::get('/export/conversations', [ExportController::class, 'exportConversations'])->name('export.conversations');
    Route::get('/documents/analytics', \App\Livewire\DocumentAnalyticsDashboard::class)->name('documents.analytics');

    // API Key Management
    Route::get('/api-keys', \App\Livewire\ApiKeyManager::class)->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::put('/api-keys/{apiKey}', [ApiKeyController::class, 'update'])->name('api-keys.update');
    Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
    Route::post('/api-keys/{apiKey}/regenerate', [ApiKeyController::class, 'regenerate'])->name('api-keys.regenerate');
});

// --- Webhook Routes (No authentication required) ---
Route::post('/webhooks/nowpayments', [\App\Http\Controllers\Webhooks\NOWPaymentsWebhookController::class, 'handle'])->name('webhooks.nowpayments');

// --- Logout Route ---
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// --- Additional Authenticated Routes ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Chat & AI Assistant (Business) Routes
    // MODIFIED: Route now points directly to the Livewire component
    Route::get('/chat', ChatInterface::class)->name('chat');
    Route::post('/plan/select', [PlanController::class, 'select'])->name('plan.select');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::post('/business', [BusinessController::class, 'store'])->name('business.store');
    Route::post('/business/telegram', [BusinessController::class, 'updateTelegram'])->name('business.telegram.update');

    Route::get('/upload', FileUpload::class)->name('upload');
    
    // Telegram Bot Builder
    Route::get('/telegram-bot-builder', TelegramBotBuilder::class)->name('telegram-bot-builder');
    Route::get('/telegram-bot-builder/{botId}', TelegramBotBuilder::class)->name('telegram-bot-builder.edit');
    
    // Telegram Bot Analytics
    Route::get('/telegram-analytics', \App\Livewire\TelegramBotAnalytics::class)->name('telegram-analytics');
    
    // Team Management
    Route::get('/team-management', \App\Livewire\TeamManagement::class)->name('team-management');

    // Groups routes
    Route::get('/groups', \App\Livewire\Groups\GroupList::class)->name('groups.index');
    Route::get('/groups/{group}', \App\Livewire\Groups\GroupChat::class)->name('groups.chat');
});

// --- Admin Routes ---
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Analytics Routes
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/analytics/data', [App\Http\Controllers\Admin\AnalyticsController::class, 'data'])->name('admin.analytics.data');
    Route::get('/analytics/users', [App\Http\Controllers\Admin\AnalyticsController::class, 'users'])->name('admin.analytics.users');
    Route::get('/analytics/revenue', [App\Http\Controllers\Admin\AnalyticsController::class, 'revenue'])->name('admin.analytics.revenue');
    Route::get('/analytics/engagement', [App\Http\Controllers\Admin\AnalyticsController::class, 'engagement'])->name('admin.analytics.engagement');
    Route::get('/analytics/growth', [App\Http\Controllers\Admin\AnalyticsController::class, 'growth'])->name('admin.analytics.growth');
    Route::get('/analytics/usage', [App\Http\Controllers\Admin\AnalyticsController::class, 'usage'])->name('admin.analytics.usage');
    Route::get('/analytics/cohorts', [App\Http\Controllers\Admin\AnalyticsController::class, 'cohorts'])->name('admin.analytics.cohorts');
    Route::get('/analytics/features', [App\Http\Controllers\Admin\AnalyticsController::class, 'features'])->name('admin.analytics.features');
    Route::get('/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('admin.analytics.export');

    // User Management Routes
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
    Route::post('/users/{user}/toggle-admin', [App\Http\Controllers\Admin\UserController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/users-stats', [App\Http\Controllers\Admin\UserController::class, 'stats'])->name('admin.users.stats');

    // Activity Logs Routes
    Route::get('/logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.logs.index');

    // System Health Routes
    Route::get('/system', [App\Http\Controllers\Admin\SystemController::class, 'index'])->name('admin.system.index');
    Route::get('/system/health', [App\Http\Controllers\Admin\SystemController::class, 'health'])->name('admin.system.health');
    Route::post('/system/clear-cache', [App\Http\Controllers\Admin\SystemController::class, 'clearCache'])->name('admin.system.clear-cache');
});

// --- Authenticated API Routes ---
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/chat/ask', [ApiChatController::class, 'ask'])->name('api.chat.ask');
});

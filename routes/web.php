<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleConnectController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Marketing Pages
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/pricing', [BillingController::class, 'pricing'])->name('pricing');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze-style)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Billing Routes (Auth required, no subscription required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->prefix('billing')->name('billing.')->group(function () {
    Route::get('/plan', [BillingController::class, 'plan'])->name('plan');
    Route::post('/checkout', [BillingController::class, 'checkout'])->name('checkout');
    Route::get('/success', [BillingController::class, 'success'])->name('success');
    Route::get('/cancel', [BillingController::class, 'cancel'])->name('cancel');
    Route::get('/manage', [BillingController::class, 'manage'])->name('manage');
    Route::post('/portal', [BillingController::class, 'portal'])->name('portal');
    Route::post('/cancel-subscription', [BillingController::class, 'cancel_subscription'])->name('cancel-subscription');
    Route::post('/resume', [BillingController::class, 'resume'])->name('resume');
});

/*
|--------------------------------------------------------------------------
| App Routes (Auth + Subscription required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant', 'subscribed'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/refresh-summary', [DashboardController::class, 'refreshSummary'])->name('dashboard.refresh-summary');

    // Business Profile
    Route::prefix('business-profile')->name('business-profile.')->group(function () {
        Route::get('/', [BusinessProfileController::class, 'index'])->name('index');
        Route::put('/update', [BusinessProfileController::class, 'update'])->name('update');
        Route::post('/refresh-recommendations', [BusinessProfileController::class, 'refreshRecommendations'])->name('refresh-recommendations');
    });

    // Google Connection
    Route::prefix('google')->name('google.')->group(function () {
        Route::get('/', [GoogleConnectController::class, 'index'])->name('index');
        Route::get('/connect', [GoogleConnectController::class, 'connect'])->name('connect');
        Route::post('/disconnect', [GoogleConnectController::class, 'disconnect'])->name('disconnect');
    });

    // Locations
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationsController::class, 'index'])->name('index');
        Route::post('/{location}/set-active', [LocationsController::class, 'setActive'])->name('set-active');
        Route::post('/sync', [LocationsController::class, 'sync'])->name('sync');
        Route::post('/sync-reviews', [LocationsController::class, 'syncReviews'])->name('sync-reviews');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewsController::class, 'index'])->name('index');
        Route::get('/{review}', [ReviewsController::class, 'show'])->name('show');
        Route::post('/{review}/draft', [ReviewsController::class, 'draft'])->name('draft');
        Route::get('/{review}/draft-status', [ReviewsController::class, 'draftStatus'])->name('draft-status');
        Route::post('/{review}/reply', [ReviewsController::class, 'reply'])->name('reply');
    });

    // Templates
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [TemplatesController::class, 'index'])->name('index');
        Route::post('/', [TemplatesController::class, 'store'])->name('store');
        Route::put('/{template}', [TemplatesController::class, 'update'])->name('update');
        Route::delete('/{template}', [TemplatesController::class, 'destroy'])->name('destroy');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('index');
        Route::put('/auto-reply', [App\Http\Controllers\SettingsController::class, 'updateAutoReply'])->name('auto-reply');
    });
});

/*
|--------------------------------------------------------------------------
| Google OAuth Callback (Auth required, no subscription check)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/auth/google/callback', [GoogleConnectController::class, 'callback'])->name('google.callback');
});

/*
|--------------------------------------------------------------------------
| Webhooks (No auth, protected by secret)
|--------------------------------------------------------------------------
*/
Route::prefix('webhooks')->group(function () {
    Route::post('/google/pubsub', [WebhookController::class, 'googlePubSub'])
        ->middleware('webhook.secret')
        ->name('webhooks.google.pubsub');
});

// Stripe webhook is handled by Cashier automatically at /stripe/webhook

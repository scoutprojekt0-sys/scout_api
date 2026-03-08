<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

// Public System Endpoints
Route::get('/ping', [SystemController::class, 'ping']);
Route::get('/live-matches/count', [SystemController::class, 'liveMatchesCount']);

// Public Discovery Endpoints
Route::get('/public/players', [DiscoveryController::class, 'publicPlayers']);
Route::get('/contracts/live', [DiscoveryController::class, 'contractsLive']);
Route::get('/player-of-week', [DiscoveryController::class, 'playerOfWeek']);
Route::get('/trending/week', [DiscoveryController::class, 'trendingWeek']);
Route::get('/rising-stars', [DiscoveryController::class, 'risingStars']);
Route::get('/club-needs', [DiscoveryController::class, 'clubNeeds']);
Route::prefix('discovery')->group(function () {
    Route::get('/manager-needs', [DiscoveryController::class, 'managerNeeds']);
});

// Public News & Billing
Route::get('/news/live', [NewsController::class, 'live']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/billing/plans', [BillingController::class, 'plans']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->middleware('throttle:auth');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->middleware('throttle:auth');

    Route::middleware(['auth:sanctum', 'reject_legacy_token', 'throttle:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('ability:profile:write');
        Route::get('/sessions', [AuthController::class, 'sessions'])->middleware('ability:profile:read');
        Route::delete('/sessions', [AuthController::class, 'logoutAll'])->middleware('ability:profile:write');
        Route::delete('/sessions/{tokenId}', [AuthController::class, 'revokeSession'])->middleware('ability:profile:write');
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateMe']);
    });
});

Route::middleware(['auth:sanctum', 'reject_legacy_token', 'throttle:api'])->group(function () {
    // System
    Route::get('/notifications/count', [SystemController::class, 'notificationsCount']);
    Route::get('/users', [SystemController::class, 'usersIndex']);

    // Players, Teams, Staff
    Route::apiResource('teams', TeamController::class)->only(['index', 'show', 'update']);
    Route::apiResource('staff', StaffController::class)->only(['index', 'show', 'update']);

    Route::post('/media', [MediaController::class, 'store'])->middleware('ability:media:write');
    Route::get('/users/{id}/media', [MediaController::class, 'indexByUser'])->middleware('ability:media:read');
    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->middleware('ability:media:write');

    Route::get('/opportunities', [OpportunityController::class, 'index']);
    Route::get('/opportunities/{id}', [OpportunityController::class, 'show']);
    Route::post('/opportunities', [OpportunityController::class, 'store'])->middleware('ability:team');
    Route::put('/opportunities/{id}', [OpportunityController::class, 'update'])->middleware('ability:team');
    Route::patch('/opportunities/{id}', [OpportunityController::class, 'update'])->middleware('ability:team');
    Route::delete('/opportunities/{id}', [OpportunityController::class, 'destroy'])->middleware('ability:team');

    Route::post('/opportunities/{id}/apply', [ApplicationController::class, 'apply'])->middleware('ability:player');
    Route::get('/applications/incoming', [ApplicationController::class, 'incoming'])->middleware('ability:team');
    Route::get('/applications/outgoing', [ApplicationController::class, 'outgoing'])->middleware('ability:player');
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'changeStatus'])->middleware('ability:team');

    Route::post('/contacts', [ContactController::class, 'store'])->middleware('ability:contact:write');
    Route::get('/contacts/inbox', [ContactController::class, 'inbox'])->middleware('ability:contact:read');
    Route::get('/contacts/sent', [ContactController::class, 'sent'])->middleware('ability:contact:read');
    Route::patch('/contacts/{id}/status', [ContactController::class, 'changeStatus'])->middleware('ability:contact:write');

    // Billing
    Route::get('/billing/subscription', [BillingController::class, 'currentSubscription']);
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe']);
    Route::post('/billing/cancel', [BillingController::class, 'cancel']);
    Route::get('/billing/payments', [BillingController::class, 'payments']);
    Route::get('/billing/invoices', [BillingController::class, 'invoices']);
});

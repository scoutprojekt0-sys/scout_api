<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContributionController;
use App\Http\Controllers\Api\DataQualityController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ModerationController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PlayerAnalyticsController;
use App\Http\Controllers\Api\PlayerCareerController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PlayerMarketValueController;
use App\Http\Controllers\Api\PlayerTransferController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\Week7AnalyticsController;
use App\Http\Controllers\Api\Week8TransparencyController;
use App\Http\Controllers\Api\Week10AnomalyController;
use App\Http\Controllers\Api\Week11WorkloadController;
use App\Http\Controllers\Api\Week12PublicTransparencyController;
use Illuminate\Support\Facades\Route;

// System endpoints
Route::get('/ping', [SystemController::class, 'ping']);

// Week 12 - Public Transparency (no auth required)
Route::get('/transparency/trust-report', [Week12PublicTransparencyController::class, 'platformTrustReport']);

// Data Quality & Trust endpoints (Week 1)
Route::prefix('data-quality')->group(function () {
    Route::get('/dashboard', [DataQualityController::class, 'dashboard']);
    Route::get('/report', [DataQualityController::class, 'report']);
    Route::get('/source-health', [Week8TransparencyController::class, 'sourceHealth']);
    Route::get('/transparency/players', [Week8TransparencyController::class, 'players']);
    Route::get('/transparency/players/{playerId}', [Week8TransparencyController::class, 'playerDetail']);
    Route::get('/audit-log', [DataQualityController::class, 'auditLog']);
    Route::get('/conflicts', [DataQualityController::class, 'conflictingData']);
    Route::get('/missing-source', [DataQualityController::class, 'missingSource']);
});

// Moderation Queue endpoints
Route::prefix('moderation')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ModerationController::class, 'index']);
    Route::get('/high-risk', [Week10AnomalyController::class, 'highRiskQueue']);
    Route::get('/stats', [ModerationController::class, 'stats']);
    Route::get('/{id}', [ModerationController::class, 'show']);
    Route::post('/{id}/score', [Week10AnomalyController::class, 'scoreQueue']);
    Route::post('/{id}/approve', [ModerationController::class, 'approve']);
    Route::post('/{id}/reject', [ModerationController::class, 'reject']);
    Route::post('/{id}/flag', [ModerationController::class, 'flag']);
});

// Player Transfer endpoints
Route::prefix('transfers')->group(function () {
    Route::get('/', [PlayerTransferController::class, 'index']);
    Route::get('/{id}', [PlayerTransferController::class, 'show']);
    Route::get('/player/{playerId}/timeline', [PlayerTransferController::class, 'timeline']);
    Route::post('/', [PlayerTransferController::class, 'store'])->middleware('auth:sanctum');
});

// Player Career Timeline endpoints
Route::prefix('career')->group(function () {
    Route::get('/player/{playerId}/timeline', [PlayerCareerController::class, 'timeline']);
    Route::get('/player/{playerId}/statistics', [PlayerCareerController::class, 'statistics']);
    Route::post('/', [PlayerCareerController::class, 'store'])->middleware('auth:sanctum');
});

// Week 6 - Player analytics endpoints
Route::prefix('players')->group(function () {
    Route::post('/compare', [PlayerAnalyticsController::class, 'compare']);
    Route::get('/{playerId}/trend-summary', [PlayerAnalyticsController::class, 'trendSummary']);
});

// Player Market Value endpoints
Route::prefix('market-values')->group(function () {
    Route::get('/', [PlayerMarketValueController::class, 'index']);
    Route::get('/leaderboard', [PlayerMarketValueController::class, 'leaderboard']);
    Route::get('/player/{playerId}/history', [PlayerMarketValueController::class, 'history']);
    Route::get('/player/{playerId}/calculate', [PlayerMarketValueController::class, 'calculate']);
    Route::get('/player/{playerId}/trends', [PlayerMarketValueController::class, 'trends']);
    Route::post('/compare', [PlayerMarketValueController::class, 'compare']);
    Route::post('/', [PlayerMarketValueController::class, 'store'])->middleware('auth:sanctum');
});

// User Contributions endpoints (Week 3)
Route::prefix('contributions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ContributionController::class, 'index']);
    Route::get('/my', [ContributionController::class, 'myContributions']);
    Route::get('/stats', [ContributionController::class, 'stats']);
    Route::get('/{id}', [ContributionController::class, 'show']);
    Route::post('/', [ContributionController::class, 'store']);
    Route::post('/{id}/approve', [ContributionController::class, 'approve']);
    Route::post('/{id}/reject', [ContributionController::class, 'reject']);
    Route::post('/{id}/request-info', [ContributionController::class, 'requestInfo']);
});
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
Route::get('/teams/{id}/overview', [TeamController::class, 'overview']);

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
    // Week 7 analytics
    Route::get('/analytics/admin-overview', [Week7AnalyticsController::class, 'adminOverview']);
    Route::get('/analytics/team/{teamId}', [Week7AnalyticsController::class, 'teamScoutingFunnel']);

    // System
    Route::get('/notifications/count', [SystemController::class, 'notificationsCount']);
    Route::get('/users', [SystemController::class, 'usersIndex']);

    // Players, Teams, Staff
    Route::apiResource('teams', TeamController::class)->only(['index', 'show', 'update']);
    Route::get('/teams/{id}/transfer-summary', [TeamController::class, 'transferSummary']);
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

    // Week 11 - Reviewer Workload & SLA
    Route::get('/analytics/reviewer-workload', [Week11WorkloadController::class, 'reviewerWorkload']);
    Route::get('/analytics/sla-dashboard', [Week11WorkloadController::class, 'slaDashboard']);
});

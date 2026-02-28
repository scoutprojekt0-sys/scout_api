<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClubNeedController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LiveMatchController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\RadarController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TransferMarketController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateMe']);
    });
});

Route::get('/news/live', [NewsController::class, 'live']);
Route::get('/matches/live', [LiveMatchController::class, 'live']);
Route::get('/transfer-market', [TransferMarketController::class, 'index']);
Route::get('/transfer-market/{id}', [TransferMarketController::class, 'show']);
Route::get('/club-needs', [ClubNeedController::class, 'index']);
Route::get('/club-needs/{id}', [ClubNeedController::class, 'show']);
Route::get('/radar/matches', [RadarController::class, 'matches']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('players', PlayerController::class)->only(['index', 'show', 'update']);
    Route::apiResource('teams', TeamController::class)->only(['index', 'show', 'update']);
    Route::apiResource('staff', StaffController::class)->only(['index', 'show', 'update']);

    Route::post('/media', [MediaController::class, 'store']);
    Route::get('/users/{id}/media', [MediaController::class, 'indexByUser']);
    Route::delete('/media/{id}', [MediaController::class, 'destroy']);

    Route::apiResource('opportunities', OpportunityController::class);
    Route::post('/transfer-market', [TransferMarketController::class, 'store']);
    Route::match(['put', 'patch'], '/transfer-market/{id}', [TransferMarketController::class, 'update']);
    Route::delete('/transfer-market/{id}', [TransferMarketController::class, 'destroy']);
    Route::post('/club-needs', [ClubNeedController::class, 'store']);
    Route::match(['put', 'patch'], '/club-needs/{id}', [ClubNeedController::class, 'update']);
    Route::delete('/club-needs/{id}', [ClubNeedController::class, 'destroy']);

    Route::post('/opportunities/{id}/apply', [ApplicationController::class, 'apply']);
    Route::get('/applications/incoming', [ApplicationController::class, 'incoming']);
    Route::get('/applications/outgoing', [ApplicationController::class, 'outgoing']);
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'changeStatus']);

    Route::post('/contacts', [ContactController::class, 'store']);
    Route::get('/contacts/inbox', [ContactController::class, 'inbox']);
    Route::get('/contacts/sent', [ContactController::class, 'sent']);
    Route::patch('/contacts/{id}/status', [ContactController::class, 'changeStatus']);
});

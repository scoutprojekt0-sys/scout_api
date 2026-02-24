<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::middleware(['auth:sanctum', 'reject_legacy_token', 'throttle:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateMe']);
    });
});

Route::get('/news/live', [NewsController::class, 'live']);

Route::middleware(['auth:sanctum', 'reject_legacy_token', 'throttle:api'])->group(function () {
    Route::apiResource('players', PlayerController::class)->only(['index', 'show', 'update']);
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
});

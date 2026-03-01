<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health/live', [HealthController::class, 'live']);
Route::get('/health/ready', [HealthController::class, 'ready']);
Route::view('/login', 'login');
Route::view('/live-scores', 'live-scores');

Route::view('/admin', 'admin-dashboard');

<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Anasayfa - Serve static index.html directly (no CORS issues)
Route::get('/', function() {
    $indexPath = base_path('../index.html');
    if (file_exists($indexPath)) {
        return response()->file($indexPath);
    }

    // Fallback to blade view
    $stats = [
        'scouts' => '15K',
        'videos' => '50K',
        'transfers' => '1,234',
        'satisfaction' => '92'
    ];
    return view('index', ['stats' => $stats]);
})->name('home');

// Live Matches sayfası
Route::get('/live-matches', function() {
    return view('live-matches');
})->name('live-matches');

// Notifications sayfası
Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications');

// Advanced Search
Route::get('/advanced-search', function() {
    return view('advanced-search');
})->name('advanced-search');

// Giriş yapanlar için dashboard
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [FrontendController::class, 'showDashboard'])->name('dashboard');

    // Role-specific dashboards
    Route::get('/dashboard/player', function() {
        return view('dashboards.player');
    })->name('dashboard.player');

    Route::get('/dashboard/scout', function() {
        return view('dashboards.scout');
    })->name('dashboard.scout');

    Route::get('/dashboard/manager', function() {
        return view('dashboards.manager');
    })->name('dashboard.manager');

    Route::get('/dashboard/club', function() {
        return view('dashboards.club');
    })->name('dashboard.club');

    Route::get('/dashboard/lawyer', function() {
        return view('dashboards.lawyer');
    })->name('dashboard.lawyer');

});

Route::get('/health/live', [HealthController::class, 'live']);
Route::get('/health/ready', [HealthController::class, 'ready']);
Route::view('/login', 'login');
Route::view('/live-scores', 'live-scores');

Route::view('/admin', 'admin-dashboard');
// Admin dashboard (public for current onboarding flow)
Route::get('/dashboard/admin', function() {
    return view('admin-dashboard');
})->name('dashboard.admin');

// Admin dashboard - Public access for testing (remove auth for demo)
Route::get('/admin', function() {
    return view('admin-dashboard');
})->name('admin.dashboard');

// Simple test route
Route::get('/admin-test', function() {
    return view('admin-test');
});

// Serve root-level static html pages from project folder (e.g. /favorites.html)
Route::get('/{page}.html', function ($page) {
    $safePage = preg_replace('/[^a-zA-Z0-9._-]/', '', (string) $page);
    $filePath = base_path('../' . $safePage . '.html');

    if (file_exists($filePath)) {
        return response()->file($filePath);
    }

    abort(404);
})->where('page', '[A-Za-z0-9._-]+');

<?php

use App\Http\Controllers\HealthController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    // Fallback to blade views for CI / minimal installs
    $stats = [
        'scouts' => '15K',
        'videos' => '50K',
        'transfers' => '1,234',
        'satisfaction' => '92'
    ];
    if (view()->exists('index')) {
        return view('index', ['stats' => $stats]);
    }

    if (view()->exists('welcome')) {
        return view('welcome');
    }

    return response('OK', 200);
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
    Route::get('/dashboard', function () {
        return view('dashboards.player');
    })->name('dashboard');

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

Route::view('/admin', 'admin-dashboard');

Route::get('/health/live', [HealthController::class, 'live']);
Route::get('/health/ready', [HealthController::class, 'ready']);

Route::get('/lang/{locale}', function (Request $request, string $locale): RedirectResponse {
    $supported = config('app.supported_locales', ['tr', 'en', 'de', 'es']);
    if (in_array($locale, $supported, true)) {
        $request->session()->put('locale', $locale);
    }

    return redirect()->back();
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

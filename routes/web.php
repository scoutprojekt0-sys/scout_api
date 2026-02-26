<?php

use App\Http\Controllers\HealthController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

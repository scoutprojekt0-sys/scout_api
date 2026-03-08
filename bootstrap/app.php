<?php

use App\Http\Middleware\RejectLegacyWildcardToken;
use App\Http\Middleware\RequestMetricsLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(RequestMetricsLogger::class);

        // API rate limiting
        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':'.env('RATE_LIMIT_API', 60).',1',
        ]);

        // Apply input sanitization to all API routes
        $middleware->api(append: [
            \App\Http\Middleware\SanitizeInput::class,
        ]);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'reject_legacy_token' => RejectLegacyWildcardToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

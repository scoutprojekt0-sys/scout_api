<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            try {
                // Log user activity in background
                dispatch(function () use ($request) {
                    try {
                        \App\Models\ActivityLog::create([
                            'user_id' => $request->user()->id,
                            'action' => $request->route()?->getName() ?? $request->method() . ' ' . $request->path(),
                            'entity_type' => null,
                            'entity_id' => null,
                            'metadata' => [
                                'method' => $request->method(),
                                'path' => $request->path(),
                                'params' => $request->route()?->parameters(),
                            ],
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ]);
                    } catch (\Exception $e) {
                        // Silently fail - don't break response
                        \Log::warning('Activity log failed: ' . $e->getMessage());
                    }
                })->afterResponse();
            } catch (\Exception $e) {
                // Silently fail - don't break response
                \Log::warning('Dispatch failed: ' . $e->getMessage());
            }
        }

        return $response;
    }
}

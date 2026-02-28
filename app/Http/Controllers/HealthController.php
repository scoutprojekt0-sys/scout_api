<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class HealthController extends Controller
{
    public function live(): JsonResponse
    {
        try {
            return response()->json([
                'ok' => true,
                'status' => 'live',
                'timestamp' => now()->toIso8601String(),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Health live check failed', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'status' => 'not_live',
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }

    public function ready(): JsonResponse
    {
        try {
            DB::select('select 1');
        } catch (Throwable $e) {
            Log::error('Health ready check failed', [
                'check' => 'database',
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'status' => 'not_ready',
                'checks' => [
                    'database' => false,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }

        return response()->json([
            'ok' => true,
            'status' => 'ready',
            'checks' => [
                'database' => true,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

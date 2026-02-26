<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    public function live(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'status' => 'live',
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function ready(): JsonResponse
    {
        try {
            DB::select('select 1');
        } catch (Throwable) {
            return response()->json([
                'ok' => false,
                'status' => 'not_ready',
                'checks' => [
                    'database' => false,
                ],
                'timestamp' => now()->toISOString(),
            ], 503);
        }

        return response()->json([
            'ok' => true,
            'status' => 'ready',
            'checks' => [
                'database' => true,
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $checks = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
        ];

        $failed = collect($checks)->contains(fn (bool $ok) => $ok === false);

        return response()->json([
            'ok' => !$failed,
            'status' => $failed ? 'not_ready' : 'ready',
            'checks' => $checks,
            'timestamp' => now()->toISOString(),
        ], $failed ? 503 : 200);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::select('select 1');
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function checkStorage(): bool
    {
        try {
            $disk = Storage::disk(config('filesystems.default'));
            $name = 'healthcheck/'.uniqid('probe_', true).'.tmp';
            $disk->put($name, 'ok');
            $disk->delete($name);
            return true;
        } catch (Throwable) {
            return false;
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileController extends Controller
{
    /**
     * Register device token for push notifications
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:255',
            'platform' => 'required|in:ios,android,web',
            'device_name' => 'nullable|string|max:150',
        ]);

        DB::table('device_tokens')->updateOrInsert(
            [
                'user_id' => $request->user()->id,
                'token' => $validated['token'],
            ],
            [
                'platform' => $validated['platform'],
                'device_name' => $validated['device_name'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Cihaz kaydedildi',
        ]);
    }

    /**
     * Get latest app version
     */
    public function getLatestVersion(Request $request): JsonResponse
    {
        $platform = $request->get('platform', 'ios');

        $version = DB::table('mobile_app_versions')
            ->where('platform', $platform)
            ->orderByDesc('released_at')
            ->first();

        return response()->json([
            'ok' => true,
            'data' => $version,
        ]);
    }

    /**
     * Check if update required
     */
    public function checkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => 'required|in:ios,android',
            'current_version' => 'required|string',
        ]);

        $latestVersion = DB::table('mobile_app_versions')
            ->where('platform', $validated['platform'])
            ->orderByDesc('released_at')
            ->first();

        $updateRequired = false;
        if ($latestVersion && version_compare($validated['current_version'], $latestVersion->version, '<')) {
            $updateRequired = $latestVersion->is_required;
        }

        return response()->json([
            'ok' => true,
            'update_available' => (bool) $latestVersion && version_compare($validated['current_version'], $latestVersion->version, '<'),
            'update_required' => $updateRequired,
            'latest_version' => $latestVersion,
        ]);
    }
}

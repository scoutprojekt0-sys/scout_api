<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'service' => 'Scout API',
        ]);
    }

    public function notificationsCount(): JsonResponse
    {
        $user = auth()->user();

        $count = Cache::remember(
            "notifications_count_{$user->id}",
            300,
            fn() => DB::table('notifications')
                ->where('user_id', $user->id)
                ->where('read_at', null)
                ->count()
        );

        return response()->json(['count' => $count]);
    }

    public function liveMatchesCount(): JsonResponse
    {
        $count = Cache::remember('live_matches_count', 60, function () {
            // Mock data for now - integrate with sports API later
            return rand(5, 15);
        });

        return response()->json(['count' => $count]);
    }

    public function usersIndex(): JsonResponse
    {
        $role = request('role');

        $users = DB::table('users')
            ->when($role, fn($q) => $q->where('role', $role))
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->paginate(20);

        return response()->json($users);
    }
}

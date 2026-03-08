<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SystemController extends Controller
{
    public function ping(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'message' => 'pong',
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function notificationsCount(Request $request): JsonResponse
    {
        $count = DB::table('notifications')
            ->where('user_id', (int) $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'ok' => true,
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }

    public function liveMatchesCount(): JsonResponse
    {
        $count = 0;

        if (Schema::hasTable('live_matches')) {
            $count = DB::table('live_matches')
                ->where('status', 'live')
                ->count();
        } elseif (Schema::hasTable('opportunities')) {
            $count = DB::table('opportunities')
                ->where('status', 'open')
                ->count();
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'count' => $count,
            ],
        ]);
    }

    public function usersIndex(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['nullable', Rule::in(['player', 'manager', 'coach', 'scout', 'team'])],
            'q' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = DB::table('users')
            ->select(['id', 'name', 'email', 'role', 'city', 'phone', 'created_at']);

        if (!empty($validated['role'])) {
            $query->where('role', $validated['role']);
        }

        if (!empty($validated['q'])) {
            $keyword = '%' . $validated['q'] . '%';
            $query->where(function ($builder) use ($keyword) {
                $builder->where('name', 'like', $keyword)
                    ->orWhere('email', 'like', $keyword)
                    ->orWhere('city', 'like', $keyword);
            });
        }

        $users = $query->orderByDesc('created_at')->paginate((int) ($validated['per_page'] ?? 20));

        return response()->json([
            'ok' => true,
            'filters' => [
                'role' => $validated['role'] ?? null,
                'q' => $validated['q'] ?? null,
            ],
            'data' => $users,
        ]);
    }
}

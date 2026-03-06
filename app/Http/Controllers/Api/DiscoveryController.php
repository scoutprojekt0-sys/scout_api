<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoostProfile;
use App\Models\DiscoveryPost;
use App\Models\PlayerViewStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DiscoveryController extends Controller
{
    public function managerNeeds(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            $authName = trim((string) optional($request->user())->name);
            $validated = $request->validate([
                'author_name' => ['nullable', 'string', 'max:255'],
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'position' => ['nullable', 'string', 'max:30'],
                'min_height' => ['nullable', 'integer', 'min:100', 'max:260'],
                'dominant_side' => ['nullable', 'string', 'max:20'],
                'age_min' => ['nullable', 'integer', 'min:10', 'max:60'],
                'age_max' => ['nullable', 'integer', 'min:10', 'max:60'],
                'free_only' => ['nullable', 'string', 'max:20'],
                'budget_min' => ['nullable', 'integer', 'min:0'],
                'budget_max' => ['nullable', 'integer', 'min:0'],
                'city' => ['nullable', 'string', 'max:80'],
            ]);

            $row = DiscoveryPost::query()->create([
                'author_role' => 'manager',
                'author_name' => !empty($validated['author_name']) ? $validated['author_name'] : ($authName !== '' ? $authName : null),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'position' => $validated['position'] ?? null,
                'min_height' => $validated['min_height'] ?? null,
                'dominant_side' => $validated['dominant_side'] ?? null,
                'age_min' => $validated['age_min'] ?? null,
                'age_max' => $validated['age_max'] ?? null,
                'free_only' => $validated['free_only'] ?? null,
                'budget_min' => $validated['budget_min'] ?? null,
                'budget_max' => $validated['budget_max'] ?? null,
                'city' => $validated['city'] ?? null,
            ]);

            return response()->json([
                'ok' => true,
                'data' => $row,
            ], 201);
        }

        $rows = DiscoveryPost::query()
            ->where('author_role', 'manager')
            ->latest()
            ->limit(300)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function coachNeeds(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            $authName = trim((string) optional($request->user())->name);
            $validated = $request->validate([
                'author_name' => ['nullable', 'string', 'max:255'],
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
            ]);

            $row = DiscoveryPost::query()->create([
                'author_role' => 'coach',
                'author_name' => !empty($validated['author_name']) ? $validated['author_name'] : ($authName !== '' ? $authName : null),
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);

            return response()->json([
                'ok' => true,
                'data' => $row,
            ], 201);
        }

        $rows = DiscoveryPost::query()
            ->where('author_role', 'coach')
            ->latest()
            ->limit(300)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function boosts(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            $authName = trim((string) optional($request->user())->name);
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'position' => ['nullable', 'string', 'max:40'],
                'city' => ['nullable', 'string', 'max:80'],
                'summary' => ['nullable', 'string'],
                'package_code' => ['nullable', 'string', 'max:20'],
                'package_label' => ['nullable', 'string', 'max:40'],
                'price_tl' => ['nullable', 'integer', 'min:0'],
                'paid' => ['nullable', 'boolean'],
                'expires_at' => ['nullable', 'date'],
                'card_last4' => ['nullable', 'string', 'max:4'],
            ]);

            $row = BoostProfile::query()->create([
                'name' => $validated['name'] ?: ($authName !== '' ? $authName : 'Oyuncu'),
                'position' => $validated['position'] ?? null,
                'city' => $validated['city'] ?? null,
                'summary' => $validated['summary'] ?? null,
                'package_code' => $validated['package_code'] ?? null,
                'package_label' => $validated['package_label'] ?? null,
                'price_tl' => $validated['price_tl'] ?? 0,
                'paid' => $validated['paid'] ?? true,
                'expires_at' => $validated['expires_at'] ?? null,
                'card_last4' => $validated['card_last4'] ?? null,
            ]);

            return response()->json([
                'ok' => true,
                'data' => $row,
            ], 201);
        }

        $rows = BoostProfile::query()
            ->latest()
            ->limit(300)
            ->get()
            ->map(static function (BoostProfile $boost): array {
                return [
                    'id' => $boost->id,
                    'name' => $boost->name,
                    'position' => $boost->position,
                    'city' => $boost->city,
                    'summary' => $boost->summary,
                    'package_label' => $boost->package_label,
                    'expires_at' => optional($boost->expires_at)->toIso8601String(),
                    'created_at' => optional($boost->created_at)->toIso8601String(),
                ];
            });

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function trackPlayerView(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_user_id' => ['nullable', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $playerUserId = $validated['player_user_id'] ?? null;
        $name = $validated['name'] ?? null;

        if ($playerUserId === null && empty($name)) {
            return response()->json([
                'ok' => false,
                'message' => 'player_user_id or name is required',
            ], 422);
        }

        $query = PlayerViewStat::query();
        $row = $playerUserId !== null
            ? $query->firstOrCreate(['player_user_id' => $playerUserId], ['name' => $name, 'views' => 0])
            : $query->firstOrCreate(['name' => $name, 'player_user_id' => null], ['views' => 0]);

        $row->views = (int) $row->views + 1;
        $row->last_viewed_at = Carbon::now();
        if (empty($row->name) && !empty($name)) {
            $row->name = $name;
        }
        $row->save();

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $row->id,
                'player_user_id' => $row->player_user_id,
                'name' => $row->name,
                'views' => $row->views,
            ],
        ]);
    }

    public function topViewedPlayers(): JsonResponse
    {
        $rows = PlayerViewStat::query()
            ->orderByDesc('views')
            ->limit(10)
            ->get(['player_user_id', 'name', 'views']);

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function weeklyDigest(): JsonResponse
    {
        $since = Carbon::now()->subDays(7);

        $weeklyPlayers = DB::table('users')
            ->where('created_at', '>=', $since)
            ->where(function ($q): void {
                $q->where('role', 'player')->orWhere('user_type', 'player');
            })
            ->count();

        $weeklyManagerNeeds = DiscoveryPost::query()
            ->where('author_role', 'manager')
            ->where('created_at', '>=', $since)
            ->count();

        $weeklyCoachNeeds = DiscoveryPost::query()
            ->where('author_role', 'coach')
            ->where('created_at', '>=', $since)
            ->count();

        $topViewed = PlayerViewStat::query()
            ->orderByDesc('views')
            ->limit(5)
            ->get(['player_user_id', 'name', 'views']);

        return response()->json([
            'ok' => true,
            'data' => [
                'weekly_player_count' => $weeklyPlayers,
                'weekly_manager_needs' => $weeklyManagerNeeds,
                'weekly_coach_needs' => $weeklyCoachNeeds,
                'top_viewed_players' => $topViewed,
            ],
        ]);
    }
}

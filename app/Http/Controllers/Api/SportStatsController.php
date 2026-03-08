<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SportSpecificStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SportStatsController extends Controller
{
    public function getPlayerStats(Request $request, int $playerUserId): JsonResponse
    {
        $stats = SportSpecificStat::where('player_user_id', $playerUserId)
            ->with('player')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $stats,
        ]);
    }

    public function getSportStats(Request $request, int $playerUserId, string $sport): JsonResponse
    {
        $validated = $request->validate([
            'sport' => ['required', 'in:football,basketball,volleyball'],
        ]);

        $stat = SportSpecificStat::where('player_user_id', $playerUserId)
            ->where('sport', $sport)
            ->first();

        if (!$stat) {
            return response()->json([
                'ok' => false,
                'message' => 'İstatistik bulunamadı.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $stat,
        ]);
    }

    public function updateStats(Request $request, int $playerUserId): JsonResponse
    {
        // Sadece kendi istatistiklerini güncelleyebilir
        if ($request->user()->id !== $playerUserId) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu istatistikleri güncelleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'sport' => ['required', 'in:football,basketball,volleyball'],
            'football_goals' => ['nullable', 'integer', 'min:0'],
            'football_assists' => ['nullable', 'integer', 'min:0'],
            'basketball_points' => ['nullable', 'integer', 'min:0'],
            'basketball_rebounds' => ['nullable', 'integer', 'min:0'],
            'basketball_assists' => ['nullable', 'integer', 'min:0'],
            'basketball_steals' => ['nullable', 'integer', 'min:0'],
            'volleyball_aces' => ['nullable', 'integer', 'min:0'],
            'volleyball_kills' => ['nullable', 'integer', 'min:0'],
            'volleyball_blocks' => ['nullable', 'integer', 'min:0'],
            'volleyball_digs' => ['nullable', 'integer', 'min:0'],
        ]);

        $stat = SportSpecificStat::updateOrCreate(
            [
                'player_user_id' => $playerUserId,
                'sport' => $validated['sport'],
            ],
            $validated
        );

        return response()->json([
            'ok' => true,
            'message' => 'İstatistikler güncellendi.',
            'data' => $stat,
        ]);
    }

    public function leaderboard(Request $request): JsonResponse
    {
        $sport = $request->input('sport');
        $limit = $request->input('limit', 20);

        $validated = $request->validate([
            'sport' => ['required', 'in:football,basketball,volleyball'],
            'limit' => ['integer', 'min:1', 'max:100'],
            'stat_type' => ['nullable', 'string'],
        ]);

        $query = SportSpecificStat::query()
            ->where('sport', $validated['sport'])
            ->with(['player.playerProfile', 'player.media'])
            ->limit($validated['limit']);

        // Spor türüne göre sıralama
        switch ($validated['sport']) {
            case 'football':
                $query->orderBy('football_goals', 'desc');
                break;
            case 'basketball':
                $query->orderBy('basketball_points', 'desc');
                break;
            case 'volleyball':
                $query->orderBy('volleyball_kills', 'desc');
                break;
        }

        $leaderboard = $query->get();

        return response()->json([
            'ok' => true,
            'sport' => $validated['sport'],
            'data' => $leaderboard,
        ]);
    }
}

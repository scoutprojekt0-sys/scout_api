<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerStatisticController extends Controller
{
    public function index(Request $request, int $playerUserId): JsonResponse
    {
        $stats = PlayerStatistic::query()
            ->where('player_user_id', $playerUserId)
            ->orderBy('season', 'desc')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $stats,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_user_id' => ['required', 'exists:users,id'],
            'season' => ['required', 'string', 'max:20'],
            'competition' => ['nullable', 'string', 'max:100'],
            'matches_played' => ['integer', 'min:0'],
            'goals' => ['integer', 'min:0'],
            'assists' => ['integer', 'min:0'],
            'yellow_cards' => ['integer', 'min:0'],
            'red_cards' => ['integer', 'min:0'],
            'minutes_played' => ['integer', 'min:0'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ]);

        // Sadece oyuncu kendi istatistiklerini ekleyebilir
        if ($request->user()->id !== $validated['player_user_id']) {
            return response()->json([
                'ok' => false,
                'message' => 'Sadece kendi istatistiklerinizi ekleyebilirsiniz.',
            ], 403);
        }

        $stat = PlayerStatistic::create($validated);

        return response()->json([
            'ok' => true,
            'message' => 'İstatistik eklendi.',
            'data' => $stat,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $stat = PlayerStatistic::findOrFail($id);

        if ($request->user()->id !== $stat->player_user_id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu istatistiği düzenleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'season' => ['sometimes', 'string', 'max:20'],
            'competition' => ['nullable', 'string', 'max:100'],
            'matches_played' => ['integer', 'min:0'],
            'goals' => ['integer', 'min:0'],
            'assists' => ['integer', 'min:0'],
            'yellow_cards' => ['integer', 'min:0'],
            'red_cards' => ['integer', 'min:0'],
            'minutes_played' => ['integer', 'min:0'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ]);

        $stat->update($validated);

        return response()->json([
            'ok' => true,
            'message' => 'İstatistik güncellendi.',
            'data' => $stat,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $stat = PlayerStatistic::findOrFail($id);

        if ($request->user()->id !== $stat->player_user_id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu istatistiği silme yetkiniz yok.',
            ], 403);
        }

        $stat->delete();

        return response()->json([
            'ok' => true,
            'message' => 'İstatistik silindi.',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerComparisonController extends Controller
{
    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_ids' => ['required', 'array', 'min:2', 'max:5'],
            'player_ids.*' => ['exists:users,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
        ]);

        $playerIds = $validated['player_ids'];
        $seasonId = $validated['season_id'] ?? null;

        $players = User::query()
            ->whereIn('id', $playerIds)
            ->with([
                'playerProfile.primaryPosition',
                'playerProfile.currentClub',
                'playerProfile.nationality',
            ])
            ->get()
            ->map(function($player) use ($seasonId) {
                // İstatistikleri al
                $stats = \App\Models\PlayerDetailedStatistic::query()
                    ->where('player_user_id', $player->id)
                    ->when($seasonId, fn($q) => $q->where('season_id', $seasonId))
                    ->first();

                // Özellikler
                $attributes = \App\Models\PlayerAttribute::where('player_user_id', $player->id)->first();

                // Piyasa değeri
                $marketValues = \App\Models\PlayerMarketValue::query()
                    ->where('player_user_id', $player->id)
                    ->latest('valuation_date')
                    ->limit(5)
                    ->get();

                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'profile' => $player->playerProfile,
                    'statistics' => $stats,
                    'attributes' => $attributes,
                    'market_values' => $marketValues,
                    'overall_rating' => $attributes?->calculateOverall() ?? 0,
                ];
            });

        // Karşılaştırma kaydını logla
        \App\Models\PlayerComparison::create([
            'user_id' => $request->user()?->id,
            'player_ids' => $playerIds,
            'season_id' => $seasonId,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'ok' => true,
            'data' => [
                'players' => $players,
                'comparison_date' => now()->toDateTimeString(),
            ],
        ]);
    }

    public function similar(int $playerUserId, Request $request): JsonResponse
    {
        $targetPlayer = User::with('playerProfile.primaryPosition')->findOrFail($playerUserId);
        $targetProfile = $targetPlayer->playerProfile;

        if (!$targetProfile) {
            return response()->json([
                'ok' => false,
                'message' => 'Oyuncu profili bulunamadı.',
            ], 404);
        }

        $limit = $request->input('limit', 10);

        // Benzer oyuncular - pozisyon, yaş, piyasa değeri bazlı
        $similar = User::query()
            ->where('id', '!=', $playerUserId)
            ->where('role', 'player')
            ->whereHas('playerProfile', function($q) use ($targetProfile) {
                $q->where('primary_position_id', $targetProfile->primary_position_id);

                // Yaş benzerliği (±3 yıl)
                if ($targetProfile->date_of_birth) {
                    $targetAge = now()->diffInYears($targetProfile->date_of_birth);
                    $q->whereBetween('date_of_birth', [
                        now()->subYears($targetAge + 3),
                        now()->subYears($targetAge - 3),
                    ]);
                }

                // Piyasa değeri benzerliği (±30%)
                if ($targetProfile->current_market_value > 0) {
                    $minValue = $targetProfile->current_market_value * 0.7;
                    $maxValue = $targetProfile->current_market_value * 1.3;
                    $q->whereBetween('current_market_value', [$minValue, $maxValue]);
                }
            })
            ->with(['playerProfile.primaryPosition', 'playerProfile.currentClub'])
            ->limit($limit)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => [
                'target_player' => $targetPlayer,
                'similar_players' => $similar,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerMarketValue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketValueController extends Controller
{
    public function playerHistory(int $playerUserId): JsonResponse
    {
        $values = PlayerMarketValue::query()
            ->where('player_user_id', $playerUserId)
            ->orderBy('valuation_date', 'desc')
            ->get();

        $currentValue = $values->first()?->market_value ?? 0;
        $highestValue = $values->max('market_value') ?? 0;
        $lowestValue = $values->min('market_value') ?? 0;

        return response()->json([
            'ok' => true,
            'data' => [
                'history' => $values,
                'summary' => [
                    'current_value' => $currentValue,
                    'highest_value' => $highestValue,
                    'lowest_value' => $lowestValue,
                    'change_percentage' => $values->count() > 1
                        ? (($currentValue - $values->last()->market_value) / $values->last()->market_value) * 100
                        : 0,
                ],
            ],
        ]);
    }

    public function addValuation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_user_id' => ['required', 'exists:users,id'],
            'market_value' => ['required', 'numeric', 'min:0'],
            'valuation_date' => ['required', 'date'],
            'currency' => ['string', 'max:3'],
            'change_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $valuation = PlayerMarketValue::create($validated);

        // Oyuncu profilindeki güncel değeri güncelle
        \App\Models\PlayerProfile::where('user_id', $validated['player_user_id'])
            ->update(['current_market_value' => $validated['market_value']]);

        return response()->json([
            'ok' => true,
            'message' => 'Piyasa değeri kaydedildi.',
            'data' => $valuation,
        ], 201);
    }

    public function mostValuable(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 100);
        $position = $request->input('position');
        $leagueId = $request->input('league_id');

        $query = User::query()
            ->where('role', 'player')
            ->whereHas('playerProfile', function($q) use ($position, $leagueId) {
                $q->where('current_market_value', '>', 0);

                if ($position) {
                    $q->whereHas('primaryPosition', function($pq) use ($position) {
                        $pq->where('short_name', $position);
                    });
                }

                if ($leagueId) {
                    $q->whereHas('currentClub.league', function($lq) use ($leagueId) {
                        $lq->where('id', $leagueId);
                    });
                }
            })
            ->with(['playerProfile.currentClub', 'playerProfile.primaryPosition', 'playerProfile.nationality'])
            ->get()
            ->sortByDesc('playerProfile.current_market_value')
            ->take($limit)
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $query,
        ]);
    }

    public function valueTrends(Request $request): JsonResponse
    {
        $period = $request->input('period', 'month'); // week, month, year
        $limit = $request->input('limit', 20);

        // En çok değer kazanan oyuncular
        $gainers = $this->calculateValueChanges('gainers', $period, $limit);

        // En çok değer kaybeden oyuncular
        $losers = $this->calculateValueChanges('losers', $period, $limit);

        return response()->json([
            'ok' => true,
            'data' => [
                'gainers' => $gainers,
                'losers' => $losers,
            ],
        ]);
    }

    private function calculateValueChanges(string $type, string $period, int $limit)
    {
        $dateFrom = match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $players = User::query()
            ->where('role', 'player')
            ->with(['playerProfile'])
            ->get()
            ->map(function($user) use ($dateFrom) {
                $currentValue = $user->playerProfile->current_market_value ?? 0;

                $previousValue = PlayerMarketValue::query()
                    ->where('player_user_id', $user->id)
                    ->where('valuation_date', '>=', $dateFrom)
                    ->orderBy('valuation_date', 'asc')
                    ->value('market_value') ?? $currentValue;

                $change = $currentValue - $previousValue;
                $changePercentage = $previousValue > 0 ? ($change / $previousValue) * 100 : 0;

                return [
                    'player' => $user,
                    'current_value' => $currentValue,
                    'previous_value' => $previousValue,
                    'change' => $change,
                    'change_percentage' => round($changePercentage, 2),
                ];
            })
            ->filter(fn($item) => $type === 'gainers' ? $item['change'] > 0 : $item['change'] < 0)
            ->sortByDesc(fn($item) => abs($item['change']))
            ->take($limit)
            ->values();

        return $players;
    }
}

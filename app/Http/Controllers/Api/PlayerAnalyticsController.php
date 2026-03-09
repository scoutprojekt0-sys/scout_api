<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerCareerTimeline;
use App\Models\PlayerMarketValue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerAnalyticsController extends Controller
{
    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_ids' => ['required', 'array', 'min:2', 'max:5'],
            'player_ids.*' => [Rule::exists('users', 'id')->where('role', 'player')],
        ]);

        $players = User::query()
            ->whereIn('id', $validated['player_ids'])
            ->get(['id', 'name', 'position', 'age', 'city']);

        $result = $players->map(function (User $player): array {
            $latestValue = PlayerMarketValue::query()
                ->where('player_id', $player->id)
                ->where('verification_status', 'verified')
                ->orderByDesc('valuation_date')
                ->first();

            $career = PlayerCareerTimeline::query()
                ->where('player_id', $player->id)
                ->where('verification_status', 'verified')
                ->get(['appearances', 'goals', 'assists', 'minutes_played']);

            $appearances = (int) $career->sum('appearances');
            $goals = (int) $career->sum('goals');
            $assists = (int) $career->sum('assists');
            $minutes = (int) $career->sum('minutes_played');

            return [
                'player_id' => $player->id,
                'player_name' => $player->name,
                'position' => $player->position,
                'age' => $player->age,
                'city' => $player->city,
                'market_value' => $latestValue?->value,
                'currency' => $latestValue?->currency ?? 'EUR',
                'value_trend' => $latestValue?->value_trend,
                'valuation_date' => $latestValue?->valuation_date?->toDateString(),
                'stats' => [
                    'appearances' => $appearances,
                    'goals' => $goals,
                    'assists' => $assists,
                    'minutes_played' => $minutes,
                    'goal_contribution' => $goals + $assists,
                    'goal_contribution_per_game' => $appearances > 0
                        ? round(($goals + $assists) / $appearances, 2)
                        : 0,
                ],
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'data' => [
                'players' => $result,
                'best_market_value' => $result->filter(fn ($p) => $p['market_value'] !== null)->sortByDesc('market_value')->first(),
                'best_goal_contribution' => $result->sortByDesc('stats.goal_contribution')->first(),
            ],
        ]);
    }

    public function trendSummary(int $playerId): JsonResponse
    {
        $player = User::query()
            ->where('id', $playerId)
            ->where('role', 'player')
            ->firstOrFail();

        $valueSeries = PlayerMarketValue::query()
            ->where('player_id', $playerId)
            ->where('verification_status', 'verified')
            ->orderBy('valuation_date')
            ->get(['valuation_date', 'value', 'value_change_percent']);

        $formSeries = PlayerCareerTimeline::query()
            ->where('player_id', $playerId)
            ->where('verification_status', 'verified')
            ->orderBy('start_date')
            ->get(['season_start', 'appearances', 'goals', 'assists', 'minutes_played']);

        $latestValue = $valueSeries->last();
        $firstValue = $valueSeries->first();

        $growthPercent = 0.0;
        if ($latestValue && $firstValue && (float) $firstValue->value > 0) {
            $growthPercent = round((((float) $latestValue->value - (float) $firstValue->value) / (float) $firstValue->value) * 100, 2);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'player' => [
                    'id' => $player->id,
                    'name' => $player->name,
                    'position' => $player->position,
                    'age' => $player->age,
                ],
                'value_series' => $valueSeries,
                'form_series' => $formSeries,
                'summary' => [
                    'latest_value' => $latestValue?->value,
                    'currency' => $latestValue?->currency ?? 'EUR',
                    'overall_growth_percent' => $growthPercent,
                    'series_points' => $valueSeries->count(),
                ],
            ],
        ]);
    }
}

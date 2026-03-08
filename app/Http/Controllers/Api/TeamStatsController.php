<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamSeasonStatistic;
use App\Models\TeamMatchSchedule;
use App\Models\TeamPlayerAvailability;
use App\Models\AmateurTeam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamStatsController extends Controller
{
    public function getTeamStats(int $teamId, Request $request): JsonResponse
    {
        $seasonId = $request->input('season_id');

        if (!$seasonId) {
            $currentSeason = \App\Models\Season::where('is_current', true)->first();
            $seasonId = $currentSeason?->id;
        }

        $stats = TeamSeasonStatistic::where('team_id', $teamId)
            ->where('season_id', $seasonId)
            ->with(['team', 'season'])
            ->first();

        if (!$stats) {
            return response()->json([
                'ok' => false,
                'message' => 'Takım istatistiği bulunamadı.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'team' => $stats->team,
                'season' => $stats->season,
                'matches' => [
                    'played' => $stats->matches_played,
                    'won' => $stats->matches_won,
                    'drawn' => $stats->matches_drawn,
                    'lost' => $stats->matches_lost,
                ],
                'goals' => [
                    'for' => $stats->goals_for,
                    'against' => $stats->goals_against,
                    'difference' => $stats->goal_difference,
                ],
                'points' => $stats->points,
                'players' => [
                    'total' => $stats->total_players,
                    'injured' => $stats->injured_players,
                    'available' => $stats->total_players - $stats->injured_players,
                ],
                'form' => $stats->recent_form,
                'last_match' => $stats->last_match_date,
                'win_rate' => $stats->win_rate . '%',
            ],
        ]);
    }

    public function updateTeamStats(Request $request, int $teamId): JsonResponse
    {
        $validated = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'matches_played' => ['integer', 'min:0'],
            'matches_won' => ['integer', 'min:0'],
            'matches_drawn' => ['integer', 'min:0'],
            'matches_lost' => ['integer', 'min:0'],
            'goals_for' => ['integer', 'min:0'],
            'goals_against' => ['integer', 'min:0'],
            'points' => ['integer', 'min:0'],
            'injured_players' => ['integer', 'min:0'],
            'recent_form' => ['nullable', 'string', 'max:15'],
        ]);

        $validated['goal_difference'] = $validated['goals_for'] - $validated['goals_against'];

        $stats = TeamSeasonStatistic::updateOrCreate(
            ['team_id' => $teamId, 'season_id' => $validated['season_id']],
            $validated
        );

        return response()->json([
            'ok' => true,
            'message' => 'Takım istatistikleri güncellendi.',
            'data' => $stats,
        ]);
    }

    public function getTeamSchedule(int $teamId, Request $request): JsonResponse
    {
        $seasonId = $request->input('season_id');

        if (!$seasonId) {
            $currentSeason = \App\Models\Season::where('is_current', true)->first();
            $seasonId = $currentSeason?->id;
        }

        $schedule = TeamMatchSchedule::where('team_id', $teamId)
            ->where('season_id', $seasonId)
            ->with(['team', 'season'])
            ->orderBy('week')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $schedule->map(function($week) {
                return [
                    'week' => $week->week,
                    'date_range' => $week->match_week_start . ' - ' . $week->match_week_end,
                    'scheduled' => $week->matches_scheduled,
                    'completed' => $week->matches_completed,
                    'pending' => $week->matches_pending,
                    'status' => $week->team_status,
                ];
            }),
        ]);
    }

    public function getTeamAvailability(int $teamId): JsonResponse
    {
        $availability = TeamPlayerAvailability::where('team_id', $teamId)
            ->with('team')
            ->first();

        if (!$availability) {
            return response()->json([
                'ok' => false,
                'message' => 'Takım oyuncu durumu bulunamadı.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'team' => $availability->team->team_name,
                'squad' => [
                    'total' => $availability->total_squad_size,
                    'available' => $availability->available_players,
                    'injured' => $availability->injured_players,
                    'suspended' => $availability->suspended_players,
                ],
                'by_position' => [
                    'goalkeepers' => $availability->goalkeeper_count,
                    'defenders' => $availability->defender_count,
                    'midfielders' => $availability->midfielder_count,
                    'forwards' => $availability->forward_count,
                ],
                'last_updated' => $availability->last_updated,
            ],
        ]);
    }

    public function updateTeamAvailability(Request $request, int $teamId): JsonResponse
    {
        $validated = $request->validate([
            'total_squad_size' => ['integer', 'min:0'],
            'available_players' => ['integer', 'min:0'],
            'injured_players' => ['integer', 'min:0'],
            'suspended_players' => ['integer', 'min:0'],
            'goalkeeper_count' => ['integer', 'min:0'],
            'defender_count' => ['integer', 'min:0'],
            'midfielder_count' => ['integer', 'min:0'],
            'forward_count' => ['integer', 'min:0'],
        ]);

        $validated['last_updated'] = now()->toDateString();

        $availability = TeamPlayerAvailability::updateOrCreate(
            ['team_id' => $teamId],
            $validated
        );

        return response()->json([
            'ok' => true,
            'message' => 'Takım oyuncu durumu güncellendi.',
            'data' => $availability,
        ]);
    }

    public function getTeamComparison(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team_ids' => ['required', 'array', 'min:2', 'max:4'],
            'team_ids.*' => ['exists:amateur_teams,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
        ]);

        $seasonId = $validated['season_id']
            ?? \App\Models\Season::where('is_current', true)->value('id');

        $stats = TeamSeasonStatistic::whereIn('team_id', $validated['team_ids'])
            ->where('season_id', $seasonId)
            ->with('team')
            ->get()
            ->map(function($stat) {
                return [
                    'team_id' => $stat->team_id,
                    'team_name' => $stat->team->team_name,
                    'matches_played' => $stat->matches_played,
                    'points' => $stat->points,
                    'win_rate' => $stat->win_rate,
                    'goals_for' => $stat->goals_for,
                    'goals_against' => $stat->goals_against,
                    'goal_difference' => $stat->goal_difference,
                ];
            });

        return response()->json([
            'ok' => true,
            'comparison' => $stats,
        ]);
    }
}

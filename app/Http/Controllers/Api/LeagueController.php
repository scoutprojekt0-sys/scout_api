<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AmateurMatchRecord;
use App\Models\League;
use App\Models\LeagueStanding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = League::query()->with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        if ($request->has('tier')) {
            $query->where('tier', $request->input('tier'));
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $leagues = $query->orderBy('tier')->orderBy('name')->get();

        return response()->json([
            'ok' => true,
            'data' => $leagues,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $league = League::with(['country', 'clubs'])->findOrFail($id);

        return response()->json([
            'ok' => true,
            'data' => $league,
        ]);
    }

    public function standings(int $leagueId, Request $request): JsonResponse
    {
        $seasonId = $request->input('season_id');

        if (!$seasonId) {
            $currentSeason = \App\Models\Season::where('is_current', true)->first();
            $seasonId = $currentSeason?->id;
        }

        $standings = LeagueStanding::query()
            ->where('league_id', $leagueId)
            ->where('season_id', $seasonId)
            ->with('club')
            ->orderBy('position')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $standings,
        ]);
    }

    public function topScorers(int $leagueId, Request $request): JsonResponse
    {
        $seasonId = $request->input('season_id');

        $topScorers = \App\Models\PlayerDetailedStatistic::query()
            ->where('league_id', $leagueId)
            ->when($seasonId, fn($q) => $q->where('season_id', $seasonId))
            ->with(['player.playerProfile', 'club'])
            ->orderBy('goals', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $topScorers,
        ]);
    }

    public function topAssists(int $leagueId, Request $request): JsonResponse
    {
        $seasonId = $request->input('season_id');

        $topAssists = \App\Models\PlayerDetailedStatistic::query()
            ->where('league_id', $leagueId)
            ->when($seasonId, fn($q) => $q->where('season_id', $seasonId))
            ->with(['player.playerProfile', 'club'])
            ->orderBy('assists', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $topAssists,
        ]);
    }

    public function amateurStandings(Request $request): JsonResponse
    {
        $leagueId = $request->input('league_id');
        $city = $request->input('city');

        $query = AmateurMatchRecord::query()
            ->with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'completed')
            ->whereNotNull('home_score')
            ->whereNotNull('away_score');

        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        if ($city) {
            $query->whereHas('homeTeam', fn ($q) => $q->where('city', $city));
        }

        $matches = $query->orderByDesc('match_date')->limit(1000)->get();
        $table = [];

        foreach ($matches as $m) {
            $homeId = (int) $m->home_team_id;
            $awayId = (int) $m->away_team_id;
            if (!$homeId || !$awayId) continue;

            if (!isset($table[$homeId])) {
                $table[$homeId] = $this->initAmateurTableRow($homeId, $m->homeTeam?->team_name ?? 'Ev Sahibi');
            }
            if (!isset($table[$awayId])) {
                $table[$awayId] = $this->initAmateurTableRow($awayId, $m->awayTeam?->team_name ?? 'Deplasman');
            }

            $hs = (int) $m->home_score;
            $as = (int) $m->away_score;

            $table[$homeId]['played']++;
            $table[$awayId]['played']++;
            $table[$homeId]['gf'] += $hs;
            $table[$homeId]['ga'] += $as;
            $table[$awayId]['gf'] += $as;
            $table[$awayId]['ga'] += $hs;

            if ($hs > $as) {
                $table[$homeId]['won']++;
                $table[$awayId]['lost']++;
                $table[$homeId]['points'] += 3;
            } elseif ($hs < $as) {
                $table[$awayId]['won']++;
                $table[$homeId]['lost']++;
                $table[$awayId]['points'] += 3;
            } else {
                $table[$homeId]['drawn']++;
                $table[$awayId]['drawn']++;
                $table[$homeId]['points']++;
                $table[$awayId]['points']++;
            }
        }

        $rows = array_values(array_map(function (array $row) {
            $row['goal_difference'] = $row['gf'] - $row['ga'];
            return $row;
        }, $table));

        usort($rows, function ($a, $b) {
            return ($b['points'] <=> $a['points'])
                ?: ($b['goal_difference'] <=> $a['goal_difference'])
                ?: ($b['gf'] <=> $a['gf'])
                ?: strcmp($a['team_name'], $b['team_name']);
        });

        foreach ($rows as $idx => &$row) {
            $row['position'] = $idx + 1;
        }

        return response()->json([
            'ok' => true,
            'data' => $rows,
            'meta' => [
                'matches_count' => $matches->count(),
                'computed_at' => now()->toIso8601String(),
            ],
        ]);
    }

    private function initAmateurTableRow(int $teamId, string $teamName): array
    {
        return [
            'team_id' => $teamId,
            'team_name' => $teamName,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'gf' => 0,
            'ga' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'position' => 0,
        ];
    }
}

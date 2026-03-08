<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiscoveryController extends Controller
{
    public function managerNeeds(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:80'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = DB::table('opportunities')
            ->join('users as teams', 'teams.id', '=', 'opportunities.team_user_id')
            ->where('opportunities.status', 'open')
            ->where(function ($builder) {
                $builder->where('opportunities.title', 'like', '%manager%')
                    ->orWhere('opportunities.position', 'like', '%manager%')
                    ->orWhere('opportunities.details', 'like', '%manager%');
            })
            ->select([
                'opportunities.id',
                'opportunities.title',
                'opportunities.position',
                'opportunities.city',
                'opportunities.details',
                'opportunities.created_at',
                'teams.id as team_user_id',
                'teams.name as team_name',
            ]);

        if (!empty($validated['city'])) {
            $query->where('opportunities.city', 'like', '%' . $validated['city'] . '%');
        }

        return response()->json([
            'ok' => true,
            'data' => $query->orderByDesc('opportunities.created_at')->paginate((int) ($validated['per_page'] ?? 20)),
        ]);
    }

    public function publicPlayers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'position' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = DB::table('users')
            ->join('player_profiles', 'player_profiles.user_id', '=', 'users.id')
            ->where('users.role', 'player')
            ->select([
                'users.id',
                'users.name',
                'users.city',
                'player_profiles.birth_year',
                'player_profiles.position',
                'player_profiles.dominant_foot',
                'player_profiles.height_cm',
                'player_profiles.current_team',
                'player_profiles.bio',
            ]);

        if (!empty($validated['position'])) {
            $query->where('player_profiles.position', 'like', '%' . $validated['position'] . '%');
        }

        if (!empty($validated['city'])) {
            $query->where('users.city', 'like', '%' . $validated['city'] . '%');
        }

        return response()->json([
            'ok' => true,
            'data' => $query->orderByDesc('users.created_at')->paginate((int) ($validated['per_page'] ?? 20)),
        ]);
    }

    public function contractsLive(): JsonResponse
    {
        if (!Schema::hasTable('contracts')) {
            return response()->json([
                'ok' => true,
                'data' => [],
            ]);
        }

        $rows = DB::table('contracts')
            ->join('users as players', 'players.id', '=', 'contracts.player_user_id')
            ->join('users as clubs', 'clubs.id', '=', 'contracts.club_user_id')
            ->leftJoin('users as managers', 'managers.id', '=', 'contracts.manager_user_id')
            ->where('contracts.status', 'active')
            ->where(function ($builder) {
                $builder->whereNull('contracts.ends_at')
                    ->orWhere('contracts.ends_at', '>', now());
            })
            ->orderByDesc('contracts.updated_at')
            ->get([
                'contracts.id',
                'contracts.title',
                'contracts.status',
                'contracts.starts_at',
                'contracts.ends_at',
                'contracts.salary',
                'contracts.currency',
                'players.id as player_id',
                'players.name as player_name',
                'clubs.id as club_id',
                'clubs.name as club_name',
                'managers.id as manager_id',
                'managers.name as manager_name',
            ]);

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function playerOfWeek(): JsonResponse
    {
        $player = $this->buildTrendingPlayerQuery()
            ->limit(1)
            ->first();

        return response()->json([
            'ok' => true,
            'data' => $player,
        ]);
    }

    public function trendingWeek(): JsonResponse
    {
        $rows = $this->buildTrendingPlayerQuery()
            ->limit(10)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function risingStars(): JsonResponse
    {
        $currentYear = (int) now()->format('Y');
        $rows = DB::table('users')
            ->join('player_profiles', 'player_profiles.user_id', '=', 'users.id')
            ->leftJoin('media', function ($join) {
                $join->on('media.user_id', '=', 'users.id')
                    ->where('media.created_at', '>=', now()->subDays(30));
            })
            ->where('users.role', 'player')
            ->whereNotNull('player_profiles.birth_year')
            ->where('player_profiles.birth_year', '>=', $currentYear - 21)
            ->groupBy(
                'users.id',
                'users.name',
                'users.city',
                'player_profiles.position',
                'player_profiles.birth_year'
            )
            ->orderByDesc(DB::raw('COUNT(media.id)'))
            ->orderByDesc('users.id')
            ->limit(12)
            ->get([
                'users.id',
                'users.name',
                'users.city',
                'player_profiles.position',
                'player_profiles.birth_year',
                DB::raw('COUNT(media.id) as monthly_media_count'),
            ]);

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function clubNeeds(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:80'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = DB::table('team_profiles')
            ->join('users', 'users.id', '=', 'team_profiles.user_id')
            ->where('users.role', 'team')
            ->whereNotNull('team_profiles.needs_text')
            ->where('team_profiles.needs_text', '!=', '')
            ->select([
                'users.id as team_user_id',
                'users.name as team_name',
                'users.city',
                'team_profiles.team_name',
                'team_profiles.league_level',
                'team_profiles.needs_text',
                'team_profiles.updated_at',
            ]);

        if (!empty($validated['city'])) {
            $query->where('users.city', 'like', '%' . $validated['city'] . '%');
        }

        return response()->json([
            'ok' => true,
            'data' => $query->orderByDesc('team_profiles.updated_at')->paginate((int) ($validated['per_page'] ?? 20)),
        ]);
    }

    private function buildTrendingPlayerQuery()
    {
        return DB::table('users')
            ->join('player_profiles', 'player_profiles.user_id', '=', 'users.id')
            ->leftJoin('media', function ($join) {
                $join->on('media.user_id', '=', 'users.id')
                    ->where('media.created_at', '>=', now()->subDays(7));
            })
            ->where('users.role', 'player')
            ->groupBy('users.id', 'users.name', 'users.city', 'player_profiles.position', 'player_profiles.current_team')
            ->orderByDesc(DB::raw('COUNT(media.id)'))
            ->orderByDesc('users.id')
            ->select([
                'users.id',
                'users.name',
                'users.city',
                'player_profiles.position',
                'player_profiles.current_team',
                DB::raw('COUNT(media.id) as weekly_media_count'),
            ]);
    }
}

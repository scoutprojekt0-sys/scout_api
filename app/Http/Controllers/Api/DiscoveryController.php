<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DiscoveryController extends Controller
{
    public function publicPlayers(): JsonResponse
    {
        $search = request('search');
        $position = request('position');
        $city = request('city');

        $players = DB::table('users')
            ->where('role', 'player')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($position, fn($q) => $q->where('position', $position))
            ->when($city, fn($q) => $q->where('city', $city))
            ->select('id', 'name', 'position', 'city', 'age', 'photo_url')
            ->paginate(20);

        return response()->json($players);
    }

    public function contractsLive(): JsonResponse
    {
        $contracts = DB::table('contracts')
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->join('users as players', 'contracts.player_id', '=', 'players.id')
            ->join('users as clubs', 'contracts.club_id', '=', 'clubs.id')
            ->select(
                'contracts.*',
                'players.name as player_name',
                'clubs.name as club_name'
            )
            ->orderBy('contracts.created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($contracts);
    }

    public function playerOfWeek(): JsonResponse
    {
        // Get player with highest rating from last week
        $player = DB::table('users')
            ->where('role', 'player')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('rating', 'desc')
            ->first();

        return response()->json($player ?: []);
    }

    public function trendingWeek(): JsonResponse
    {
        $trending = DB::table('users')
            ->where('role', 'player')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json($trending);
    }

    public function risingStars(): JsonResponse
    {
        $stars = DB::table('users')
            ->where('role', 'player')
            ->whereNotNull('age')
            ->where('age', '<=', 21)
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

        return response()->json($stars);
    }

    public function clubNeeds(): JsonResponse
    {
        $needs = DB::table('opportunities')
            ->where('status', 'open')
            ->join('users as teams', 'opportunities.team_user_id', '=', 'teams.id')
            ->where('teams.role', 'team')
            ->select('opportunities.*', 'teams.name as team_name')
            ->orderBy('opportunities.created_at', 'desc')
            ->paginate(20);

        return response()->json($needs);
    }

    public function managerNeeds(): JsonResponse
    {
        $needs = DB::table('opportunities')
            ->where('status', 'open')
            ->join('users as teams', 'opportunities.team_user_id', '=', 'teams.id')
            ->where('teams.role', 'manager')
            ->select('opportunities.*', 'teams.name as manager_name')
            ->orderBy('opportunities.created_at', 'desc')
            ->paginate(20);

        return response()->json($needs);
    }
}

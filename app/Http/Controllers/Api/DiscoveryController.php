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
        $country = request('country');

        $players = DB::table('users')
            ->where('role', 'player')
            ->where('is_public', true)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($position, fn($q) => $q->where('position', $position))
            ->when($country, fn($q) => $q->where('country', $country))
            ->select('id', 'name', 'position', 'country', 'age', 'photo_url')
            ->paginate(20);

        return response()->json($players);
    }

    public function contractsLive(): JsonResponse
    {
        $contracts = DB::table('contracts')
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->join('users', 'contracts.player_id', '=', 'users.id')
            ->select('contracts.*', 'users.name as player_name')
            ->orderBy('contracts.created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($contracts);
    }

    public function playerOfWeek(): JsonResponse
    {
        // Mock data - integrate with real analytics later
        $player = DB::table('users')
            ->where('role', 'player')
            ->inRandomOrder()
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
            ->where('age', '<=', 21)
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

        return response()->json($stars);
    }

    public function clubNeeds(): JsonResponse
    {
        $needs = DB::table('opportunities')
            ->where('type', 'club_need')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($needs);
    }

    public function managerNeeds(): JsonResponse
    {
        $needs = DB::table('opportunities')
            ->where('type', 'manager_need')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($needs);
    }
}

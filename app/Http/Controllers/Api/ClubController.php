<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\League;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Club::query()->with(['country', 'league']);

        if ($request->has('league_id')) {
            $query->where('league_id', $request->input('league_id'));
        }

        if ($request->has('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Sıralama
        $sortBy = $request->input('sort_by', 'total_market_value');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $clubs = $query->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $clubs,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $club = Club::with([
            'country',
            'league',
            'players.user',
            'marketValues' => fn($q) => $q->latest('valuation_date')->limit(10),
        ])->findOrFail($id);

        return response()->json([
            'ok' => true,
            'data' => $club,
        ]);
    }

    public function squad(int $id, Request $request): JsonResponse
    {
        $club = Club::findOrFail($id);

        $players = $club->players()
            ->with(['user', 'primaryPosition', 'nationality'])
            ->get()
            ->map(function($profile) {
                return [
                    'id' => $profile->user_id,
                    'name' => $profile->user->name,
                    'position' => $profile->primaryPosition?->name,
                    'jersey_number' => $profile->jersey_number,
                    'age' => $profile->date_of_birth ? now()->diffInYears($profile->date_of_birth) : null,
                    'nationality' => $profile->nationality?->name,
                    'market_value' => $profile->current_market_value,
                    'contract_expires' => $profile->contract_expires,
                ];
            });

        $summary = [
            'total_players' => $players->count(),
            'total_market_value' => $players->sum('market_value'),
            'average_age' => $players->avg(fn($p) => $p['age']),
            'average_market_value' => $players->avg('market_value'),
        ];

        return response()->json([
            'ok' => true,
            'data' => [
                'club' => $club,
                'squad' => $players,
                'summary' => $summary,
            ],
        ]);
    }

    public function mostValuable(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 50);

        $clubs = Club::query()
            ->with(['country', 'league'])
            ->where('total_market_value', '>', 0)
            ->orderBy('total_market_value', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $clubs,
        ]);
    }

    public function transfers(int $id, Request $request): JsonResponse
    {
        $controller = new TransferController();
        return $controller->clubActivity($id, $request);
    }
}

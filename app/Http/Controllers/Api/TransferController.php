<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\Club;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transfer::query()
            ->with(['player.playerProfile', 'fromClub', 'toClub', 'season']);

        // Filtreleme
        if ($request->has('player_user_id')) {
            $query->where('player_user_id', $request->input('player_user_id'));
        }

        if ($request->has('club_id')) {
            $clubId = $request->input('club_id');
            $query->where(function($q) use ($clubId) {
                $q->where('from_club_id', $clubId)
                  ->orWhere('to_club_id', $clubId);
            });
        }

        if ($request->has('season_id')) {
            $query->where('season_id', $request->input('season_id'));
        }

        if ($request->has('transfer_type')) {
            $query->where('transfer_type', $request->input('transfer_type'));
        }

        $transfers = $query->latest('transfer_date')->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $transfers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_user_id' => ['required', 'exists:users,id'],
            'from_club_id' => ['nullable', 'exists:clubs,id'],
            'to_club_id' => ['nullable', 'exists:clubs,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'transfer_date' => ['required', 'date'],
            'transfer_type' => ['required', 'in:transfer,loan,free,end_of_loan,retirement'],
            'transfer_fee' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['string', 'max:3'],
            'market_value_at_time' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'loan_end_date' => ['nullable', 'date', 'after:transfer_date'],
            'option_to_buy' => ['boolean'],
        ]);

        $transfer = Transfer::create($validated);
        $transfer->load(['player', 'fromClub', 'toClub', 'season']);

        return response()->json([
            'ok' => true,
            'message' => 'Transfer kaydedildi.',
            'data' => $transfer,
        ], 201);
    }

    public function playerHistory(int $playerUserId): JsonResponse
    {
        $transfers = Transfer::query()
            ->where('player_user_id', $playerUserId)
            ->with(['fromClub', 'toClub', 'season'])
            ->orderBy('transfer_date', 'desc')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $transfers,
        ]);
    }

    public function clubActivity(int $clubId, Request $request): JsonResponse
    {
        $seasonId = $request->input('season_id');

        $incoming = Transfer::query()
            ->where('to_club_id', $clubId)
            ->when($seasonId, fn($q) => $q->where('season_id', $seasonId))
            ->with(['player.playerProfile', 'fromClub'])
            ->latest('transfer_date')
            ->get();

        $outgoing = Transfer::query()
            ->where('from_club_id', $clubId)
            ->when($seasonId, fn($q) => $q->where('season_id', $seasonId))
            ->with(['player.playerProfile', 'toClub'])
            ->latest('transfer_date')
            ->get();

        $totalSpent = $incoming->sum('transfer_fee');
        $totalEarned = $outgoing->sum('transfer_fee');
        $balance = $totalEarned - $totalSpent;

        return response()->json([
            'ok' => true,
            'data' => [
                'incoming' => $incoming,
                'outgoing' => $outgoing,
                'summary' => [
                    'total_spent' => $totalSpent,
                    'total_earned' => $totalEarned,
                    'balance' => $balance,
                ],
            ],
        ]);
    }
}

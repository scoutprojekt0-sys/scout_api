<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubNeedsController extends Controller
{
    /**
     * Kulüp ihtiyaçlarını listele
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('club_needs')
            ->join('users', 'club_needs.club_user_id', '=', 'users.id')
            ->where('club_needs.is_active', true)
            ->select('club_needs.*', 'users.name as club_name')
            ->orderByDesc('club_needs.urgency')
            ->orderByDesc('club_needs.created_at');

        // Filter by position
        if ($request->has('position')) {
            $query->where('club_needs.position', $request->position);
        }

        // Filter by urgency
        if ($request->has('urgency')) {
            $query->where('club_needs.urgency', $request->urgency);
        }

        $needs = $query->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $needs,
        ]);
    }

    /**
     * En acil ihtiyaçlar
     */
    public function urgent(): JsonResponse
    {
        $urgentNeeds = DB::table('club_needs')
            ->join('users', 'club_needs.club_user_id', '=', 'users.id')
            ->where('club_needs.is_active', true)
            ->where('club_needs.urgency', 'urgent')
            ->whereNull('club_needs.deleted_at')
            ->select('club_needs.*', 'users.name as club_name')
            ->orderByDesc('club_needs.created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $urgentNeeds,
        ]);
    }

    /**
     * Kulüp ihtiyacı oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'position' => 'required|string|max:50',
            'urgency' => 'in:low,medium,high,urgent',
            'contract_type' => 'nullable|in:transfer,loan,free_agent',
            'min_age' => 'nullable|integer|min:16|max:50',
            'max_age' => 'nullable|integer|min:16|max:50',
            'budget_min' => 'nullable|numeric',
            'budget_max' => 'nullable|numeric',
            'description' => 'nullable|string|max:2000',
            'deadline' => 'nullable|date',
        ]);

        $need = DB::table('club_needs')->insertGetId(array_merge($validated, [
            'club_user_id' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return response()->json([
            'ok' => true,
            'message' => 'İhtiyaç başarıyla oluşturuldu',
            'data' => ['id' => $need],
        ], 201);
    }

    /**
     * Pozisyona göre toplu ihtiyaçlar
     */
    public function byPosition(string $position): JsonResponse
    {
        $needs = DB::table('club_needs')
            ->join('users', 'club_needs.club_user_id', '=', 'users.id')
            ->where('club_needs.position', $position)
            ->where('club_needs.is_active', true)
            ->select('club_needs.*', 'users.name as club_name')
            ->orderByDesc('club_needs.urgency')
            ->get();

        return response()->json([
            'ok' => true,
            'position' => $position,
            'count' => $needs->count(),
            'data' => $needs,
        ]);
    }
}

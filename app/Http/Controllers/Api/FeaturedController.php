<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturedController extends Controller
{
    /**
     * Öne çıkan içerikler (ana sayfa)
     */
    public function getFeatured(): JsonResponse
    {
        $featured = DB::table('featured_content')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('featured_from')
                  ->orWhere('featured_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('featured_until')
                  ->orWhere('featured_until', '>=', now());
            })
            ->where('section', 'homepage')
            ->orderByDesc('priority')
            ->limit(10)
            ->get();

        // Get actual objects
        $results = $featured->map(function ($item) {
            $model = $item->featurable_type;
            $object = $model::find($item->featurable_id);

            return [
                'id' => $item->id,
                'badge_text' => $item->badge_text,
                'badge_color' => $item->badge_color,
                'priority' => $item->priority,
                'data' => $object,
            ];
        });

        return response()->json([
            'ok' => true,
            'data' => $results,
        ]);
    }

    /**
     * Yükselen yıldızlar
     */
    public function getRisingStars(): JsonResponse
    {
        $stars = DB::table('rising_stars')
            ->join('users', 'rising_stars.player_user_id', '=', 'users.id')
            ->where('rising_stars.is_featured', true)
            ->select('rising_stars.*', 'users.name', 'users.email')
            ->orderByDesc('rising_stars.growth_score')
            ->limit(10)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $stars,
        ]);
    }

    /**
     * Gündemdeki transferler
     */
    public function getHotTransfers(): JsonResponse
    {
        $transfers = DB::table('hot_transfers')
            ->join('users as player', 'hot_transfers.player_user_id', '=', 'player.id')
            ->leftJoin('users as from_club', 'hot_transfers.from_club_id', '=', 'from_club.id')
            ->leftJoin('users as to_club', 'hot_transfers.to_club_id', '=', 'to_club.id')
            ->whereIn('hot_transfers.status', ['negotiating', 'agreed'])
            ->where('hot_transfers.reliability_score', '>=', 70)
            ->select(
                'hot_transfers.*',
                'player.name as player_name',
                'from_club.name as from_club_name',
                'to_club.name as to_club_name'
            )
            ->orderByDesc('hot_transfers.reliability_score')
            ->orderByDesc('hot_transfers.views_count')
            ->limit(15)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $transfers,
        ]);
    }

    /**
     * Haftanın oyuncusu
     */
    public function getPlayerOfWeek(): JsonResponse
    {
        $award = DB::table('player_awards')
            ->join('users', 'player_awards.player_user_id', '=', 'users.id')
            ->where('player_awards.award_type', 'week')
            ->where('player_awards.is_active', true)
            ->where('player_awards.period_end', '>=', now()->subDays(7))
            ->select('player_awards.*', 'users.name', 'users.email')
            ->orderByDesc('player_awards.votes_count')
            ->first();

        return response()->json([
            'ok' => true,
            'data' => $award,
        ]);
    }

    public function adminList(Request $request): JsonResponse
    {
        $section = (string) $request->query('section', 'homepage');
        $rows = DB::table('featured_content')
            ->where('section', $section)
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }

    public function adminStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'featurable_type' => ['required', 'string', 'max:255'],
            'featurable_id' => ['required', 'integer', 'min:1'],
            'section' => ['required', 'in:homepage,players,clubs,news,videos'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'badge_color' => ['nullable', 'string', 'max:20'],
            'featured_from' => ['nullable', 'date'],
            'featured_until' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exists = DB::table('featured_content')
            ->where('featurable_type', $validated['featurable_type'])
            ->where('featurable_id', $validated['featurable_id'])
            ->where('section', $validated['section'])
            ->first();

        $payload = [
            'priority' => $validated['priority'] ?? 0,
            'badge_text' => $validated['badge_text'] ?? null,
            'badge_color' => $validated['badge_color'] ?? '#3B82F6',
            'featured_from' => $validated['featured_from'] ?? null,
            'featured_until' => $validated['featured_until'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'updated_at' => now(),
        ];

        if ($exists) {
            DB::table('featured_content')->where('id', $exists->id)->update($payload);
            $id = (int) $exists->id;
        } else {
            $id = (int) DB::table('featured_content')->insertGetId(array_merge($payload, [
                'featurable_type' => $validated['featurable_type'],
                'featurable_id' => $validated['featurable_id'],
                'section' => $validated['section'],
                'created_at' => now(),
            ]));
        }

        return response()->json([
            'ok' => true,
            'data' => ['id' => $id],
        ], $exists ? 200 : 201);
    }

    public function adminToggleActive(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $affected = DB::table('featured_content')
            ->where('id', $id)
            ->update([
                'is_active' => (bool) $validated['is_active'],
                'updated_at' => now(),
            ]);

        if ($affected === 0) {
            return response()->json([
                'ok' => false,
                'message' => 'Kayit bulunamadi',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Durum guncellendi',
        ]);
    }
}

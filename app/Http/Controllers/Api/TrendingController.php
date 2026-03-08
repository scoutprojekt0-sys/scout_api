<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrendingController extends Controller
{
    /**
     * Bugünün en çok tıklananları
     */
    public function getTodayTrending(Request $request): JsonResponse
    {
        $type = $request->get('type', 'all'); // all, players, videos, news

        $query = DB::table('trending_content')
            ->where('trending_date', today())
            ->orderByDesc('trending_score');

        if ($type !== 'all') {
            $query->where('trendable_type', 'like', "%{$type}%");
        }

        $trending = $query->limit(20)->get();

        // Get full objects
        $results = $trending->map(function ($item) {
            $model = $item->trendable_type;
            $object = $model::find($item->trendable_id);

            return [
                'id' => $item->trendable_id,
                'type' => $item->trendable_type,
                'views_today' => $item->views_today,
                'clicks_today' => $item->clicks_today,
                'trending_score' => $item->trending_score,
                'data' => $object,
            ];
        });

        return response()->json([
            'ok' => true,
            'data' => $results,
        ]);
    }

    /**
     * Haftalık trendler
     */
    public function getWeeklyTrending(): JsonResponse
    {
        $trending = DB::table('trending_content')
            ->where('trending_date', '>=', now()->subDays(7))
            ->select('trendable_type', 'trendable_id')
            ->selectRaw('SUM(views_week) as total_views')
            ->selectRaw('SUM(clicks_week) as total_clicks')
            ->groupBy('trendable_type', 'trendable_id')
            ->orderByDesc('total_views')
            ->limit(20)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $trending,
        ]);
    }

    /**
     * Track view/click
     */
    public function trackInteraction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'id' => 'required|integer',
            'action' => 'required|in:view,click,share,save',
        ]);

        $trendableType = $validated['type'];
        $trendableId = $validated['id'];
        $action = $validated['action'];

        DB::table('trending_content')->updateOrInsert(
            [
                'trendable_type' => $trendableType,
                'trendable_id' => $trendableId,
                'trending_date' => today(),
            ],
            [
                "{$action}s_today" => DB::raw("{$action}s_today + 1"),
                "{$action}s_week" => DB::raw("{$action}s_week + 1"),
                "{$action}s_month" => DB::raw("{$action}s_month + 1"),
                'last_viewed_at' => now(),
                'trending_score' => DB::raw('trending_score + ' . $this->getScoreIncrement($action)),
                'updated_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    private function getScoreIncrement(string $action): float
    {
        return match($action) {
            'view' => 1.0,
            'click' => 2.0,
            'share' => 5.0,
            'save' => 3.0,
            default => 1.0,
        };
    }
}

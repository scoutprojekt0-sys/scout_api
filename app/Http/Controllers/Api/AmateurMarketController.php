<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AmateurPlayerMarketValue;
use App\Models\MarketPointLog;
use App\Models\WeeklyTrendingPlayer;
use App\Models\AmateurMarketStatistics;
use App\Models\AmateurTransferOffer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmateurMarketController extends Controller
{
    // ═════════════════════════════════════════════
    // OYUNCU PİYASA DEĞERİ DETAYLARI
    // ═════════════════════════════════════════════

    public function getPlayerMarketValue(int $playerId): JsonResponse
    {
        $marketValue = AmateurPlayerMarketValue::where('player_id', $playerId)
            ->firstOrFail();

        return response()->json([
            'ok' => true,
            'data' => [
                'player_id' => $marketValue->player_id,
                'market_value' => $marketValue->calculated_market_value,
                'base_value' => $marketValue->base_value,
                'points_breakdown' => [
                    'profile_views' => $marketValue->profile_views_points,
                    'engagement' => $marketValue->engagement_points,
                    'performance' => $marketValue->performance_points,
                    'trending' => $marketValue->trending_points,
                    'scout_interest' => $marketValue->scout_interest_points,
                ],
                'trend' => [
                    'status' => $marketValue->trend_status,
                    'percentage' => $marketValue->price_trend . '%',
                ],
                'rank' => $marketValue->market_rank,
                'last_updated' => $marketValue->last_updated,
            ],
        ]);
    }

    // ═════════════════════════════════════════════
    // PROFİL TIKLANDİĞİNDE PUAN EKLE
    // ═════════════════════════════════════════════

    public function recordProfileView(int $playerId): JsonResponse
    {
        $marketValue = AmateurPlayerMarketValue::firstOrCreate(
            ['player_id' => $playerId],
            ['base_value' => 5000]
        );

        // +1 Profil Görünüm Puanı
        $marketValue->addPoints('profile_view', 1, 'Profil görüntülendi');

        return response()->json([
            'ok' => true,
            'message' => 'Profil görünüm puanı eklendi',
            'market_value' => $marketValue->calculated_market_value,
        ]);
    }

    // ═════════════════════════════════════════════
    // BEĞENME, YORUM, KAYDETME İŞLEMLERİ
    // ═════════════════════════════════════════════

    public function recordEngagement(int $playerId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:like,comment,save'],
        ]);

        $marketValue = AmateurPlayerMarketValue::firstOrCreate(
            ['player_id' => $playerId],
            ['base_value' => 5000]
        );

        // Puanları Ekle
        $points = match($validated['action']) {
            'like' => 1,
            'comment' => 2,
            'save' => 1,
        };

        $marketValue->addPoints(
            $validated['action'],
            $points,
            "{$validated['action']} yapıldı"
        );

        return response()->json([
            'ok' => true,
            'message' => "Oyuncu {$validated['action']} puanı eklendi",
            'market_value' => $marketValue->calculated_market_value,
        ]);
    }

    // ═════════════════════════════════════════════
    // MAÇTAN SONRA PERFORMANS PUANLARI
    // ═════════════════════════════════════════════

    public function recordMatchPerformance(int $playerId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:goal,assist,mvp'],
        ]);

        $marketValue = AmateurPlayerMarketValue::firstOrCreate(
            ['player_id' => $playerId],
            ['base_value' => 5000]
        );

        $points = match($validated['action']) {
            'goal' => 5,
            'assist' => 3,
            'mvp' => 10,
        };

        $marketValue->addPoints(
            'match_' . $validated['action'],
            $points,
            "Maçta {$validated['action']} kaydı"
        );

        return response()->json([
            'ok' => true,
            'message' => "Oyuncu maç performans puanı eklendi",
            'market_value' => $marketValue->calculated_market_value,
        ]);
    }

    // ═════════════════════════════════════════════
    // SCOUT İLGİSİ
    // ═════════════════════════════════════════════

    public function recordScoutInterest(int $playerId): JsonResponse
    {
        $marketValue = AmateurPlayerMarketValue::firstOrCreate(
            ['player_id' => $playerId],
            ['base_value' => 5000]
        );

        // Scout Bakışı = +2 Puan
        // Scout İlgi = +5 Puan
        $marketValue->addPoints('scout_viewed', 2, 'Scout profili görüntüledi');

        return response()->json([
            'ok' => true,
            'message' => 'Scout ilgi puanı eklendi',
            'market_value' => $marketValue->calculated_market_value,
        ]);
    }

    // ═════════════════════════════════════════════
    // OYUNCU PİYASA SİRALAMASI
    // ═════════════════════════════════════════════

    public function getMarketLeaderboard(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 50);

        $leaderboard = AmateurPlayerMarketValue::with('player')
            ->orderByDesc('calculated_market_value')
            ->limit($limit)
            ->get()
            ->map(function($market, $index) {
                return [
                    'rank' => $index + 1,
                    'player_id' => $market->player_id,
                    'player_name' => $market->player->name,
                    'market_value' => $market->calculated_market_value,
                    'trend' => $market->trend_status,
                    'trend_percent' => $market->price_trend . '%',
                ];
            });

        return response()->json([
            'ok' => true,
            'data' => $leaderboard,
        ]);
    }

    // ═════════════════════════════════════════════
    // HAFTALIK TRENDLER
    // ═════════════════════════════════════════════

    public function getWeeklyTrending(): JsonResponse
    {
        $currentWeek = WeeklyTrendingPlayer::where('week_start', '<=', today())
            ->where('week_end', '>=', today())
            ->with('player')
            ->orderByDesc('weekly_points')
            ->limit(10)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $currentWeek,
        ]);
    }

    // ═════════════════════════════════════════════
    // PUAN GEÇMİŞİ
    // ═════════════════════════════════════════════

    public function getPlayerPointsHistory(int $playerId): JsonResponse
    {
        $history = MarketPointLog::where('player_id', $playerId)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get()
            ->map(fn($log) => [
                'action' => $log->action_type,
                'points' => $log->points_gained,
                'description' => $log->description,
                'time' => $log->created_at->diffForHumans(),
            ]);

        return response()->json([
            'ok' => true,
            'data' => $history,
        ]);
    }

    // ═════════════════════════════════════════════
    // PİYASA İSTATİSTİKLERİ
    // ═════════════════════════════════════════════

    public function getMarketStatistics(): JsonResponse
    {
        $today = today();
        $stats = AmateurMarketStatistics::whereDate('statistics_date', $today)->first();

        if (!$stats) {
            // Günlük istatistikleri hesapla
            $stats = new AmateurMarketStatistics([
                'total_players' => AmateurPlayerMarketValue::count(),
                'active_players' => AmateurPlayerMarketValue::where('last_updated', '>=', now()->subDays(30))->count(),
                'average_market_value' => AmateurPlayerMarketValue::avg('calculated_market_value'),
                'highest_value' => AmateurPlayerMarketValue::max('calculated_market_value'),
                'lowest_value' => AmateurPlayerMarketValue::min('calculated_market_value'),
                'trending_up_count' => AmateurPlayerMarketValue::where('trend_status', 'up')->count(),
                'trending_down_count' => AmateurPlayerMarketValue::where('trend_status', 'down')->count(),
                'stable_count' => AmateurPlayerMarketValue::where('trend_status', 'stable')->count(),
                'statistics_date' => $today,
            ]);
            $stats->save();
        }

        return response()->json([
            'ok' => true,
            'data' => $stats,
        ]);
    }

    // ═════════════════════════════════════════════
    // TRANSFER TEKLİFİ
    // ═════════════════════════════════════════════

    public function sendTransferOffer(int $playerId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'offer_message' => ['required', 'string', 'max:500'],
            'proposed_value' => ['nullable', 'integer', 'min:1000'],
        ]);

        $offer = AmateurTransferOffer::create([
            'player_id' => $playerId,
            'from_team_id' => $request->user()->id,
            'offer_message' => $validated['offer_message'],
            'proposed_value' => $validated['proposed_value'],
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Transfer teklifi gönderildi',
            'data' => $offer,
        ]);
    }

    public function respondToTransferOffer(int $offerId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'response' => ['required', 'in:accepted,rejected'],
        ]);

        $offer = AmateurTransferOffer::findOrFail($offerId);

        $offer->update([
            'status' => $validated['response'],
            'responded_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => "Transfer teklifi {$validated['response']}",
        ]);
    }
}

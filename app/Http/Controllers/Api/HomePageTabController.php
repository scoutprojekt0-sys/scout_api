<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerProfileCard;
use App\Models\ManagerProfileCard;
use App\Models\CoachProfileCard;
use App\Models\LiveMatch;
use App\Models\News;
use App\Models\League;
use App\Models\AmateurPlayerMarketValue;
use App\Models\WeeklyTrendingPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomePageTabController extends Controller
{
    // ═════════════════════════════════════════════
    // ANASAYFAda 3 TAB/BUTON
    // ═════════════════════════════════════════════
    // 1. ⚽ SCOUT PLATFORM
    // 2. 🎯 RADAR
    // 3. 💰 TRANSFERMARKET

    /**
     * TAB 1: ⚽ SCOUT PLATFORM
     * BUTONUN ALTI:
     * - Oyuncu Keşfi (Top Oyuncular)
     * - Menajer Profilleri
     * - Antrenör Profilleri
     * - Avukat Profilleri
     * - Amatör Futbol
     * - Deneme Maçı
     */
    public function getScoutPlatformTab(Request $request): JsonResponse
    {
        // TAB 1: ⚽ SCOUT PLATFORM ALTINDAKİ İÇERİKLER

        // 1.1 OYUNCU KEŞFİ (Profil Kartları)
        $topPlayers = PlayerProfileCard::where('is_public', true)
            ->where('is_verified', true)
            ->orderByDesc('overall_rating')
            ->limit(6)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->full_name,
                'sport' => $p->sport,
                'position' => $p->position,
                'rating' => $p->overall_rating,
                'age' => $p->age,
            ]);

        // 1.2 MENAJER PROFİLLERİ
        $topManagers = ManagerProfileCard::where('is_public', true)
            ->where('is_verified', true)
            ->orderByDesc('overall_rating')
            ->limit(3)
            ->get();

        // 1.3 ANTRENÖR PROFİLLERİ
        $topCoaches = CoachProfileCard::where('is_public', true)
            ->where('is_verified', true)
            ->orderByDesc('overall_rating')
            ->limit(3)
            ->get();

        // 1.4 AMATÖR FUTBOL OYUNCULARI
        $amateurPlayers = PlayerProfileCard::where('user_type', 'amateur')
            ->where('is_public', true)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return response()->json([
            'ok' => true,
            'tab' => 'scout_platform',
            'data' => [
                'section_1' => [
                    'title' => '🔍 OYUNCU KEŞFİ',
                    'subtitle' => 'En İyi Oyuncuları Keşfet',
                    'type' => 'profile_cards',
                    'data' => $topPlayers,
                ],
                'section_2' => [
                    'title' => '👔 MENAJER PROFİLLERİ',
                    'subtitle' => 'Profesyonel Menajerler',
                    'type' => 'manager_cards',
                    'data' => $topManagers,
                ],
                'section_3' => [
                    'title' => '🎯 ANTRENÖR PROFİLLERİ',
                    'subtitle' => 'Deneyimli Antrenörler',
                    'type' => 'coach_cards',
                    'data' => $topCoaches,
                ],
                'section_4' => [
                    'title' => '⚽ AMATÖR FUTBOL',
                    'subtitle' => 'Amatör Oyuncu Yıldızları',
                    'type' => 'amateur_cards',
                    'data' => $amateurPlayers,
                ],
            ],
        ]);
    }

    /**
     * TAB 2: 🎯 RADAR
     * BUTONUN ALTI:
     * - Haftalık Trendler (Trending Up/Down)
     * - Canlı Maçlar & Sonuçlar
     * - Lig Tablosu
     * - Haberler
     * - Scout Aktiviteleri
     */
    public function getRadarTab(Request $request): JsonResponse
    {
        // TAB 2: 🎯 RADAR ALTINDAKİ İÇERİKLER

        // 2.1 HAFTALIK TRENDLER
        $trendingPlayers = WeeklyTrendingPlayer::where('week_start', '<=', today())
            ->where('week_end', '>=', today())
            ->with('player')
            ->orderByDesc('weekly_points')
            ->limit(5)
            ->get();

        // 2.2 CANLI MAÇLAR
        $liveMatches = LiveMatch::where('is_finished', false)
            ->where('is_live', true)
            ->limit(5)
            ->get();

        // 2.3 LİG TABLOSU
        $leagues = League::with('standings')
            ->limit(3)
            ->get();

        // 2.4 HABERLER
        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return response()->json([
            'ok' => true,
            'tab' => 'radar',
            'data' => [
                'section_1' => [
                    'title' => '🔥 HAFTALIK TRENDLER',
                    'subtitle' => 'En Popüler Oyuncular',
                    'type' => 'trending',
                    'data' => $trendingPlayers,
                ],
                'section_2' => [
                    'title' => '⚽ CANLI MAÇLAR',
                    'subtitle' => 'Şu An Oynanan Maçlar',
                    'type' => 'live_matches',
                    'data' => $liveMatches,
                ],
                'section_3' => [
                    'title' => '🏆 LİG TABLOLARI',
                    'subtitle' => 'Puan Durumları',
                    'type' => 'league_standings',
                    'data' => $leagues,
                ],
                'section_4' => [
                    'title' => '📰 HABERLER',
                    'subtitle' => 'Son Haberler',
                    'type' => 'news',
                    'data' => $news,
                ],
            ],
        ]);
    }

    /**
     * TAB 3: 💰 TRANSFERMARKET
     * BUTONUN ALTI:
     * - Profesyonel Piyasa Değeri
     * - AMATÖR PİYASA (Tıkla Puan!)
     * - Leaderboard (Top 50)
     * - Trend Analizi
     * - Transfer Haberleri
     */
    public function getTransferMarketTab(Request $request): JsonResponse
    {
        // TAB 3: 💰 TRANSFERMARKET ALTINDAKİ İÇERİKLER

        // 3.1 PROFESYONEL OYUNCU PİYASA DEĞERİ
        $professionalPlayers = PlayerProfileCard::where('is_public', true)
            ->where('user_type', 'professional')
            ->orderByDesc('overall_rating')
            ->limit(10)
            ->get();

        // 3.2 AMATÖR PİYASA (TIKLANDI PUAN!)
        $amateurMarketLeaders = AmateurPlayerMarketValue::orderByDesc('calculated_market_value')
            ->limit(10)
            ->with('player')
            ->get()
            ->map(fn($m) => [
                'player_id' => $m->player_id,
                'player_name' => $m->player->name,
                'market_value' => $m->calculated_market_value,
                'trend' => $m->trend_status,
                'trend_percent' => $m->price_trend,
                'points_breakdown' => [
                    'profile_views' => $m->profile_views_points,
                    'engagement' => $m->engagement_points,
                    'performance' => $m->performance_points,
                ],
            ]);

        // 3.3 TRANSFER HABERLERİ
        $transferNews = News::where('is_published', true)
            ->where('category', 'transfer')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return response()->json([
            'ok' => true,
            'tab' => 'transfermarket',
            'data' => [
                'section_1' => [
                    'title' => '💎 PROFESYONEL PİYASA',
                    'subtitle' => 'Piyasa Değeri (Futbolcu/Menajer/Antrenör)',
                    'type' => 'professional_market',
                    'data' => $professionalPlayers,
                ],
                'section_2' => [
                    'title' => '🏪 AMATÖR PİYASA (TIKLANDI PUAN!)',
                    'subtitle' => 'Profil Tıklandıkça Değer Artan Oyuncular',
                    'type' => 'amateur_market',
                    'instructions' => 'Oyuncuyu tıkla → +1 Puan | Beğen → +1 Puan | Gol at → +5 Puan',
                    'data' => $amateurMarketLeaders,
                ],
                'section_3' => [
                    'title' => '📈 PAZAR TRENDLERI',
                    'subtitle' => 'Yükselen & Düşen Değerler',
                    'type' => 'market_trends',
                    'data' => [
                        'trending_up' => AmateurPlayerMarketValue::where('trend_status', 'up')
                            ->orderByDesc('price_trend')
                            ->limit(5)
                            ->count(),
                        'trending_down' => AmateurPlayerMarketValue::where('trend_status', 'down')
                            ->orderByDesc('price_trend')
                            ->limit(5)
                            ->count(),
                        'stable' => AmateurPlayerMarketValue::where('trend_status', 'stable')->count(),
                    ],
                ],
                'section_4' => [
                    'title' => '📰 TRANSFER HABERLERİ',
                    'subtitle' => 'Son Transfer Gelişmeleri',
                    'type' => 'transfer_news',
                    'data' => $transferNews,
                ],
            ],
        ]);
    }

    /**
     * ANASAYFA - TÜM TABLAR (İlk Açılışta SCOUT PLATFORM tab'ı açık)
     */
    public function getHomePageAllTabs(Request $request): JsonResponse
    {
        $defaultTab = $request->input('tab', 'scout_platform');

        $tabs = [
            'scout_platform' => $this->getScoutPlatformTab($request)->json()['data'],
            'radar' => $this->getRadarTab($request)->json()['data'],
            'transfermarket' => $this->getTransferMarketTab($request)->json()['data'],
        ];

        return response()->json([
            'ok' => true,
            'tabs' => [
                'available_tabs' => [
                    [
                        'id' => 'scout_platform',
                        'label' => '⚽ SCOUT PLATFORM',
                        'icon' => '🔍',
                        'description' => 'Oyuncu Keşfi & Profil Yönetimi',
                    ],
                    [
                        'id' => 'radar',
                        'label' => '🎯 RADAR',
                        'icon' => '📊',
                        'description' => 'Trendler, Canlı Maçlar & Haberler',
                    ],
                    [
                        'id' => 'transfermarket',
                        'label' => '💰 TRANSFERMARKET',
                        'icon' => '💎',
                        'description' => 'Piyasa Değeri & Transfer',
                    ],
                ],
                'default_active_tab' => $defaultTab,
                'tab_contents' => $tabs,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PlayerProfileCard;
use App\Models\LiveMatch;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // ═════════════════════════════════════════════
    // PUBLIC ANASAYFA (Giriş Yapmamış Kullanıcılar)
    // ═════════════════════════════════════════════

    /**
     * Render public homepage (HTML view)
     */
    public function renderPublicHome()
    {
        $stats = [
            'active_scouts' => '15K',
            'videos' => '50K',
            'transfers' => '1,234'
        ];

        return view('welcome', ['stats' => $stats]);
    }

    public function getPublicHome(): JsonResponse
    {
        // Popüler Oyuncular
        $popularPlayers = PlayerProfileCard::where('is_public', true)
            ->where('is_verified', true)
            ->orderByDesc('overall_rating')
            ->limit(6)
            ->get();

        // Canlı Maçlar
        $liveMatches = LiveMatch::where('is_finished', false)
            ->where('is_live', true)
            ->limit(5)
            ->get();

        // Yaklaşan Maçlar
        $upcomingMatches = LiveMatch::where('is_finished', false)
            ->where('match_date', '>=', now())
            ->where('is_live', false)
            ->orderBy('match_date')
            ->limit(5)
            ->get();

        // Haberler
        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        // İstatistikler
        $statistics = [
            'total_players' => PlayerProfileCard::where('is_public', true)->count(),
            'total_matches_played' => LiveMatch::where('is_finished', true)->count(),
            'total_users' => User::count(),
        ];

        return response()->json([
            'ok' => true,
            'data' => [
                'popular_players' => $popularPlayers,
                'live_matches' => $liveMatches,
                'upcoming_matches' => $upcomingMatches,
                'news' => $news,
                'statistics' => $statistics,
                'message' => 'Hoş Geldiniz! Üye olmak için kayıt yapın.',
            ],
        ]);
    }

    // ═════════════════════════════════════════════
    // AUTHENTİCATED DASHBOARD (Giriş Yapanlar)
    // Sidebar + Kısmi İçerik
    // ═════════════════════════════════════════════

    public function getAuthenticatedDashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Kullanıcı Profil Kartı
        $profileCard = null;
        $userStats = [];

        if ($user->role === 'player') {
            $profileCard = PlayerProfileCard::where('user_id', $user->id)->first();
            if ($profileCard) {
                $userStats = [
                    'sport' => $profileCard->sport,
                    'position' => $profileCard->position,
                    'rating' => $profileCard->overall_rating,
                    'views' => $profileCard->viewers_count,
                ];
            }
        }

        // Önerilen Oyuncular (Kısmi - 3 Card)
        $recommendations = PlayerProfileCard::where('user_id', '!=', $user->id)
            ->where('is_public', true)
            ->orderByDesc('overall_rating')
            ->limit(3)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->full_name,
                'sport' => $p->sport,
                'position' => $p->position,
                'rating' => $p->overall_rating,
            ]);

        // Canlı Maçlar (Kısmi - 3 Match)
        $liveMatches = LiveMatch::where('is_finished', false)
            ->where('is_live', true)
            ->limit(3)
            ->get();

        // Haberler (Kısmi - 3 News)
        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'stats' => $userStats,
                ],
                'profile_card' => $profileCard,
                'recommendations' => $recommendations,
                'live_matches' => $liveMatches,
                'news' => $news,
                'sidebar_links' => [
                    ['icon' => '🏠', 'label' => 'Ana Sayfa', 'href' => '/dashboard'],
                    ['icon' => '🔍', 'label' => 'Oyuncu Ara', 'href' => '/search'],
                    ['icon' => '📱', 'label' => 'Mesajlarım', 'href' => '/messages'],
                    ['icon' => '⭐', 'label' => 'Favorilerim', 'href' => '/favorites'],
                    ['icon' => '📊', 'label' => 'İstatistiklerim', 'href' => '/statistics'],
                    ['icon' => '⚙️', 'label' => 'Ayarlar', 'href' => '/settings'],
                ],
                'message' => 'Hoş Geldiniz! Tam dashboard için "Ana Sayfa"ya tıklayın.',
            ],
        ]);
    }

    // ═════════════════════════════════════════════
    // FULL DASHBOARD (Ana Sayfa Tıklandıktan Sonra)
    // ═════════════════════════════════════════════

    public function getFullDashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Dashboard Controller'daki getDashboard'u çağır
        $dashboardController = new DashboardController();
        return $dashboardController->getDashboard($request);
    }

    // ═════════════════════════════════════════════
    // HABERLER
    // ═════════════════════════════════════════════

    public function getNews(Request $request): JsonResponse
    {
        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(10);

        return response()->json([
            'ok' => true,
            'data' => $news,
        ]);
    }
}

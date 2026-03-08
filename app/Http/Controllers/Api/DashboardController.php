<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PlayerProfileCard;
use App\Models\ManagerProfileCard;
use App\Models\CoachProfileCard;
use App\Models\Notification;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\ProfileCardView;
use App\Models\LiveMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getDashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Temel Kullanıcı Bilgileri
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        // Role'a göre profil kartı
        $profileCard = null;
        $stats = [];

        if ($user->role === 'player') {
            $profileCard = PlayerProfileCard::where('user_id', $user->id)->first();
            if ($profileCard) {
                $stats = [
                    'views' => $profileCard->viewers_count,
                    'favorites' => $profileCard->favorites_count,
                    'likes' => $profileCard->interactions()
                        ->where('interaction_type', 'like')
                        ->count(),
                    'comments' => $profileCard->interactions()
                        ->where('interaction_type', 'comment')
                        ->count(),
                    'rating' => $profileCard->overall_rating,
                ];
            }
        } elseif ($user->role === 'manager') {
            $profileCard = ManagerProfileCard::where('user_id', $user->id)->first();
            if ($profileCard) {
                $stats = [
                    'teams_managed' => $profileCard->teams_managed,
                    'players_developed' => $profileCard->players_developed,
                    'win_rate' => $profileCard->win_rate,
                    'followers' => $profileCard->followers_count,
                    'rating' => $profileCard->overall_rating,
                ];
            }
        } elseif ($user->role === 'coach') {
            $profileCard = CoachProfileCard::where('user_id', $user->id)->first();
            if ($profileCard) {
                $stats = [
                    'players_trained' => $profileCard->players_trained,
                    'years_experience' => $profileCard->years_experience,
                    'success_rate' => $profileCard->success_rate,
                    'followers' => $profileCard->followers_count,
                    'rating' => $profileCard->overall_rating,
                ];
            }
        }

        // Son İstatistikler
        $profileViews = ProfileCardView::where('card_owner_user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Okunmamış Bildirimler
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->limit(5)
            ->latest()
            ->get();

        // Okunmamış Mesajlar
        $unreadMessages = Conversation::where(function($q) use ($user) {
                $q->where('user_1_id', $user->id)
                  ->where('user_1_read', false);
            })
            ->orWhere(function($q) use ($user) {
                $q->where('user_2_id', $user->id)
                  ->where('user_2_read', false);
            })
            ->count();

        // Son Aktiviteler
        $recentActivity = $this->getRecentActivity($user);

        // Önerilen Oyuncular/Takımlar
        $recommendations = $this->getRecommendations($user);

        // Haberler
        $news = $this->getNews($user);

        // Yaklaşan Maçlar
        $upcomingMatches = $this->getUpcomingMatches($user);

        return response()->json([
            'ok' => true,
            'data' => [
                'user' => $userData,
                'profile_card' => $profileCard,
                'stats' => $stats,
                'profile_views_this_week' => $profileViews,
                'unread_notifications' => [
                    'count' => Notification::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->count(),
                    'list' => $unreadNotifications,
                ],
                'unread_messages_count' => $unreadMessages,
                'recent_activity' => $recentActivity,
                'recommendations' => $recommendations,
                'news' => $news,
                'upcoming_matches' => $upcomingMatches,
            ],
        ]);
    }

    private function getRecentActivity($user): array
    {
        $activities = [];

        // Son Bildirimler
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($n) {
                return [
                    'type' => 'notification',
                    'title' => $n->title,
                    'message' => $n->message,
                    'time' => $n->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($n->type),
                ];
            })
            ->toArray();

        return array_merge($activities, $notifications);
    }

    private function getRecommendations($user): array
    {
        $recommendations = [];

        if ($user->role === 'player') {
            // Futbolcuya Takım Önerileri
            $recommendations = PlayerProfileCard::where('user_id', '!=', $user->id)
                ->where('is_public', true)
                ->orderByDesc('overall_rating')
                ->limit(6)
                ->get()
                ->map(function($player) {
                    return [
                        'id' => $player->id,
                        'name' => $player->full_name,
                        'position' => $player->position,
                        'sport' => $player->sport,
                        'rating' => $player->overall_rating,
                        'photo' => $player->profile_photo_url,
                    ];
                })
                ->toArray();
        } elseif ($user->role === 'manager') {
            // Menajere Oyuncu Önerileri
            $recommendations = PlayerProfileCard::where('is_public', true)
                ->where('is_verified', true)
                ->orderByDesc('overall_rating')
                ->limit(6)
                ->get()
                ->map(function($player) {
                    return [
                        'id' => $player->id,
                        'name' => $player->full_name,
                        'position' => $player->position,
                        'sport' => $player->sport,
                        'rating' => $player->overall_rating,
                        'photo' => $player->profile_photo_url,
                    ];
                })
                ->toArray();
        }

        return $recommendations;
    }

    private function getNews($user): array
    {
        $news = [];

        if ($user->role === 'player') {
            $news = [
                [
                    'title' => 'Yeni Transfer Haberleri',
                    'description' => '5 Oyuncu transferi haberleri',
                    'category' => 'transfer',
                    'time' => '2 saat önce',
                ],
                [
                    'title' => 'Lig Puan Durumu Güncellendi',
                    'description' => 'Superlig son sonuçları',
                    'category' => 'league',
                    'time' => '5 saat önce',
                ],
            ];
        } elseif ($user->role === 'manager') {
            $news = [
                [
                    'title' => 'Transfer Penceresi Bilgisi',
                    'description' => 'Yaz transferi kuralları',
                    'category' => 'transfer',
                    'time' => '1 saat önce',
                ],
                [
                    'title' => 'Oyuncu Piyasa Değerleri',
                    'description' => 'En değerli oyuncular listesi',
                    'category' => 'market',
                    'time' => '3 saat önce',
                ],
            ];
        }

        return $news;
    }

    private function getUpcomingMatches($user): array
    {
        $matches = LiveMatch::where('is_finished', false)
            ->where('match_date', '>=', now())
            ->orderBy('match_date')
            ->limit(5)
            ->get()
            ->map(function($match) {
                return [
                    'id' => $match->id,
                    'home_team' => $match->home_team,
                    'away_team' => $match->away_team,
                    'date' => $match->match_date->format('d.m.Y H:i'),
                    'league' => $match->league,
                ];
            })
            ->toArray();

        return $matches;
    }

    private function getNotificationIcon($type): string
    {
        return match($type) {
            'message' => '📨',
            'profile_viewed' => '👁️',
            'interest_shown' => '💌',
            'match_result' => '⚽',
            'league_update' => '📰',
            'coach_offer' => '🤝',
            'system_alert' => '⚠️',
            'achievement' => '🏆',
            'team_invite' => '👥',
            default => '📢',
        };
    }
}

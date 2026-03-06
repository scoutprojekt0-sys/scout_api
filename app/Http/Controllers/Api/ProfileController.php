<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PlayerProfile;
use App\Models\PlayerProfileCard;
use App\Models\PlayerStatistic;
use App\Models\PlayerVideoPortfolio;
use App\Models\ManagerProfileCard;
use App\Models\CoachProfileCard;
use App\Models\ProfilePageSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // Public oyuncu listesi (arama/sıralama için)
    public function publicPlayers(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->query('limit', 100), 500));
        $q = trim((string) $request->query('q', ''));
        $sport = trim((string) $request->query('sport', ''));
        $transferCategory = trim((string) $request->query('transfer_category', ''));
        $scoutRadar = trim((string) $request->query('scout_radar', ''));
        $positionFilter = trim((string) $request->query('position', ''));
        $leagueFilter = trim((string) $request->query('league', ''));
        $ratingMin = (float) $request->query('rating_min', 0);
        $ageMin = (int) $request->query('age_min', 0);
        $ageMax = (int) $request->query('age_max', 0);

        $users = User::query()
            ->where('role', 'player')
            ->select(['id', 'name', 'created_at'])
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $ids = $users->pluck('id')->all();
        if (empty($ids)) {
            return response()->json([
                'ok' => true,
                'data' => [],
            ]);
        }

        $cards = PlayerProfileCard::whereIn('user_id', $ids)->get()->keyBy('user_id');
        $profiles = PlayerProfile::whereIn('user_id', $ids)->get()->keyBy('user_id');
        $latestStats = PlayerStatistic::whereIn('player_user_id', $ids)
            ->orderByDesc('id')
            ->get()
            ->groupBy('player_user_id')
            ->map(fn ($group) => $group->first());

        $players = $users->map(function (User $user) use ($cards, $profiles, $latestStats) {
            $card = $cards->get($user->id);
            $profile = $profiles->get($user->id);
            $latest = $latestStats->get($user->id);

            $sportRaw = strtolower((string) ($card->sport ?? $profile->sport ?? 'football'));
            $sport = match ($sportRaw) {
                'football' => 'futbol',
                'basketball' => 'basketbol',
                'volleyball' => 'voleybol',
                default => $sportRaw,
            };

            $position = (string) ($card->position ?? $profile->position ?? 'Oyuncu');
            $club = (string) ($profile->current_team ?? '-');
            $rating = (float) ($card->overall_rating ?? $latest?->rating ?? 0);
            $age = (int) ($card->age ?? $profile?->age ?? 0);

            $hasContractDate = !empty($profile?->contract_expires);
            $contractActiveByDate = $hasContractDate && $profile->contract_expires?->isFuture();
            $hasCurrentTeam = trim((string) ($profile->current_team ?? '')) !== '';
            $contractStatus = ($contractActiveByDate || $hasCurrentTeam) ? 'active' : 'free';
            $seekingClub = $contractStatus === 'free';

            $radarNew = $user->created_at && $user->created_at->gt(now()->subDays(30));
            $radarRising = $rating >= 7.5;
            $radarHidden = ((int) ($card->viewers_count ?? 0) < 250) && $rating >= 7.0;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'sport' => $sport,
                'position' => $position,
                'age' => $age,
                'height' => $card->height ?? $profile->height_cm,
                'league' => (string) ($card->sport_level ?? '-'),
                'club' => $club,
                'rating' => $rating,
                'nationality' => (string) ($profile?->nationality?->name ?? '-'),
                'profile_photo_url' => $card->profile_photo_url,
                'contract_status' => $contractStatus,
                'seeking_club' => $seekingClub,
                'radar_new' => $radarNew,
                'radar_rising' => $radarRising,
                'radar_hidden' => $radarHidden,
            ];
        });

        if ($sport !== '') {
            $players = $players->filter(fn ($p) => strtolower((string) $p['sport']) === strtolower($sport));
        }

        if ($q !== '') {
            $qLower = mb_strtolower($q);
            $players = $players->filter(function ($p) use ($qLower) {
                return str_contains(mb_strtolower((string) $p['name']), $qLower)
                    || str_contains(mb_strtolower((string) $p['position']), $qLower)
                    || str_contains(mb_strtolower((string) $p['club']), $qLower);
            });
        }

        if ($positionFilter !== '') {
            $positionLower = mb_strtolower($positionFilter);
            $players = $players->filter(
                fn ($p) => mb_strtolower((string) $p['position']) === $positionLower
            );
        }

        if ($leagueFilter !== '') {
            $leagueLower = mb_strtolower($leagueFilter);
            $players = $players->filter(
                fn ($p) => str_contains(mb_strtolower((string) $p['league']), $leagueLower)
            );
        }

        if ($ratingMin > 0) {
            $players = $players->filter(fn ($p) => (float) $p['rating'] >= $ratingMin);
        }

        if ($ageMin > 0 || $ageMax > 0) {
            $players = $players->filter(function ($p) use ($ageMin, $ageMax) {
                $age = (int) ($p['age'] ?? 0);
                if ($age <= 0) {
                    return false;
                }
                if ($ageMin > 0 && $age < $ageMin) {
                    return false;
                }
                if ($ageMax > 0 && $age > $ageMax) {
                    return false;
                }
                return true;
            });
        }

        $transferCategoryLower = mb_strtolower($transferCategory);
        if ($transferCategoryLower === 'sozlesmeli') {
            $players = $players->filter(fn ($p) => (string) ($p['contract_status'] ?? '') === 'active');
        } elseif ($transferCategoryLower === 'bosta') {
            $players = $players->filter(fn ($p) => (string) ($p['contract_status'] ?? '') === 'free');
        } elseif ($transferCategoryLower === 'kulup-ariyor') {
            $players = $players->filter(fn ($p) => (bool) ($p['seeking_club'] ?? false) === true);
        }

        $scoutRadarLower = mb_strtolower($scoutRadar);
        if ($scoutRadarLower === 'yeni-eslesenler') {
            $players = $players->filter(fn ($p) => (bool) ($p['radar_new'] ?? false) === true);
        } elseif ($scoutRadarLower === 'yukselenler') {
            $players = $players->filter(fn ($p) => (bool) ($p['radar_rising'] ?? false) === true);
        } elseif ($scoutRadarLower === 'gizli-yetenek') {
            $players = $players->filter(fn ($p) => (bool) ($p['radar_hidden'] ?? false) === true);
        }

        return response()->json([
            'ok' => true,
            'data' => array_values($players->toArray()),
        ]);
    }

    // Public oyuncu profili (giriş gerektirmez)
    public function publicPlayerProfile(int $userId): JsonResponse
    {
        $user = User::where('id', $userId)
            ->where('role', 'player')
            ->first();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Oyuncu bulunamadı.',
            ], 404);
        }

        $settings = ProfilePageSettings::where('user_id', $user->id)->first();
        if ($settings && $settings->is_profile_public === false) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu profil gizlidir.',
            ], 403);
        }

        $profile = PlayerProfile::where('user_id', $user->id)->first();
        $card = PlayerProfileCard::where('user_id', $user->id)->first();

        $latestStat = PlayerStatistic::where('player_user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        $statsSummary = [
            'matches' => (int) PlayerStatistic::where('player_user_id', $user->id)->sum('matches_played'),
            'goals' => (int) PlayerStatistic::where('player_user_id', $user->id)->sum('goals'),
            'assists' => (int) PlayerStatistic::where('player_user_id', $user->id)->sum('assists'),
            'rating' => round((float) (PlayerStatistic::where('player_user_id', $user->id)->avg('rating') ?? 0), 1),
        ];

        $videos = PlayerVideoPortfolio::where('player_user_id', $user->id)
            ->where('is_public', true)
            ->orderByDesc('id')
            ->take(6)
            ->get([
                'id',
                'title',
                'description',
                'video_url',
                'thumbnail_url',
                'views',
                'likes',
                'recorded_date',
            ]);

        return response()->json([
            'ok' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'profile' => $profile,
                'card' => $card,
                'stats' => [
                    'summary' => $statsSummary,
                    'latest' => $latestStat,
                ],
                'videos' => $videos,
            ],
        ]);
    }

    // Kendi Profili
    public function getMyProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        // Role'a göre detaylı profil
        if ($user->role === 'player') {
            $card = PlayerProfileCard::where('user_id', $user->id)->first();
            if ($card) {
                $profileData['card'] = $card;
            }
        } elseif ($user->role === 'manager') {
            $card = ManagerProfileCard::where('user_id', $user->id)->first();
            if ($card) {
                $profileData['card'] = $card;
            }
        } elseif ($user->role === 'coach') {
            $card = CoachProfileCard::where('user_id', $user->id)->first();
            if ($card) {
                $profileData['card'] = $card;
            }
        }

        // Profil Ayarları
        $settings = ProfilePageSettings::where('user_id', $user->id)->first()
            ?? ProfilePageSettings::create(['user_id' => $user->id]);

        $profileData['settings'] = $settings;

        return response()->json([
            'ok' => true,
            'data' => $profileData,
        ]);
    }

    // Başka Kullanıcının Profilini Görüntüle
    public function viewProfile(int $userId, Request $request): JsonResponse
    {
        $user = User::findOrFail($userId);
        $currentUser = $request->user();

        // Gizlilik kontrolü
        $settings = ProfilePageSettings::where('user_id', $userId)->first();
        if ($settings && !$settings->is_profile_public && $currentUser->id !== $userId) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu profil gizli.',
            ], 403);
        }

        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
        ];

        // Email gizlenmiş mi?
        if (!($settings && $settings->hide_email)) {
            $profileData['email'] = $user->email;
        }

        // Role'a göre profil
        if ($user->role === 'player') {
            $card = PlayerProfileCard::where('user_id', $user->id)->first();
            if ($card) {
                $profileData['card'] = $card;
                $profileData['type'] = 'player';
                $profileData['show_contact'] = $settings?->show_contact_button ?? true;
                $profileData['show_message'] = $settings?->show_message_button ?? true;
            }
        }

        return response()->json([
            'ok' => true,
            'data' => $profileData,
        ]);
    }

    // Profil Ayarlarını Güncelle
    public function updateProfileSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'show_contact_button' => ['nullable', 'boolean'],
            'show_message_button' => ['nullable', 'boolean'],
            'show_profile_views' => ['nullable', 'boolean'],
            'show_statistics' => ['nullable', 'boolean'],
            'allow_direct_message' => ['nullable', 'boolean'],
            'allow_profile_view' => ['nullable', 'boolean'],
            'is_profile_public' => ['nullable', 'boolean'],
            'hide_email' => ['nullable', 'boolean'],
            'hide_phone' => ['nullable', 'boolean'],
        ]);

        $settings = ProfilePageSettings::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return response()->json([
            'ok' => true,
            'message' => 'Profil ayarları güncellendi.',
            'data' => $settings,
        ]);
    }
}

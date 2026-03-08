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
    public function publicQualitySummary(): JsonResponse
    {
        $profiles = PlayerProfile::query()
            ->select(['user_id', 'position', 'bio', 'current_market_value', 'updated_at'])
            ->get();
        $cards = PlayerProfileCard::query()
            ->select(['user_id', 'profile_photo_url', 'overall_rating', 'updated_at'])
            ->get()
            ->keyBy('user_id');

        $rows = $profiles->map(function ($profile) use ($cards) {
            $card = $cards->get($profile->user_id);
            $score = collect([
                !empty($profile->position),
                !empty($profile->bio),
                !empty($card?->profile_photo_url),
                (float) ($card?->overall_rating ?? 0) > 0,
                (float) ($profile->current_market_value ?? 0) > 0,
            ])->filter(fn ($v) => $v === true)->count() * 20;

            $updatedAt = $profile->updated_at ?? $card?->updated_at;
            return [
                'score' => $score,
                'updated_at' => $updatedAt,
            ];
        });

        $total = $rows->count();
        $high = $rows->where('score', '>=', 80)->count();
        $medium = $rows->whereBetween('score', [50, 79])->count();
        $low = $rows->where('score', '<', 50)->count();
        $recent = $rows->filter(fn ($row) => !empty($row['updated_at']) && now()->diffInDays($row['updated_at']) <= 7)->count();

        return response()->json([
            'ok' => true,
            'data' => [
                'total_profiles' => $total,
                'quality_high' => $high,
                'quality_medium' => $medium,
                'quality_low' => $low,
                'updated_last_7_days' => $recent,
            ],
        ]);
    }

    public function publicPlayers(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->query('limit', 100), 500));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min((int) $request->query('per_page', 24), 100));
        $q = trim((string) $request->query('q', ''));
        $sport = trim((string) $request->query('sport', ''));
        $transferCategory = trim((string) $request->query('transfer_category', ''));
        $scoutRadar = trim((string) $request->query('scout_radar', ''));
        $positionFilter = trim((string) $request->query('position', ''));
        $leagueFilter = trim((string) $request->query('league', ''));
        $contractStatusFilter = trim((string) $request->query('contract_status', ''));
        $ratingMin = (float) $request->query('rating_min', 0);
        $ageMin = (int) $request->query('age_min', 0);
        $ageMax = (int) $request->query('age_max', 0);
        $marketMin = (float) $request->query('market_min', 0);
        $marketMax = (float) $request->query('market_max', 0);
        $qualityMin = (int) $request->query('quality_min', 0);
        $sort = trim((string) $request->query('sort', 'rating_desc'));

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
            $marketValue = (float) ($profile->current_market_value ?? 0);

            $hasContractDate = !empty($profile?->contract_expires);
            $contractActiveByDate = $hasContractDate && $profile->contract_expires?->isFuture();
            $hasCurrentTeam = trim((string) ($profile->current_team ?? '')) !== '';
            $contractStatus = ($contractActiveByDate || $hasCurrentTeam) ? 'active' : 'free';
            $seekingClub = $contractStatus === 'free';

            $radarNew = $user->created_at && $user->created_at->gt(now()->subDays(30));
            $radarRising = $rating >= 7.5;
            $radarHidden = ((int) ($card->viewers_count ?? 0) < 250) && $rating >= 7.0;
            $qualityScore = collect([
                !empty($position),
                $age > 0,
                !empty($club) && $club !== '-',
                $rating > 0,
                $marketValue > 0,
                !empty($card?->profile_photo_url),
                !empty($profile?->bio),
            ])->filter(fn ($v) => $v === true)->count() * 14;
            $qualityScore = min(100, $qualityScore);

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
                'market_value' => $marketValue,
                'nationality' => (string) ($profile?->nationality?->name ?? '-'),
                'profile_photo_url' => $card->profile_photo_url,
                'contract_status' => $contractStatus,
                'seeking_club' => $seekingClub,
                'radar_new' => $radarNew,
                'radar_rising' => $radarRising,
                'radar_hidden' => $radarHidden,
                'quality_score' => $qualityScore,
                'data_source' => 'NextScout API',
                'updated_at' => optional($profile?->updated_at ?? $card?->updated_at ?? $user->updated_at)->toIso8601String(),
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

        if ($marketMin > 0) {
            $players = $players->filter(fn ($p) => (float) ($p['market_value'] ?? 0) >= $marketMin);
        }

        if ($marketMax > 0) {
            $players = $players->filter(fn ($p) => (float) ($p['market_value'] ?? 0) <= $marketMax);
        }

        if ($qualityMin > 0) {
            $players = $players->filter(fn ($p) => (int) ($p['quality_score'] ?? 0) >= $qualityMin);
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
        if ($contractStatusFilter !== '') {
            $filterStatus = mb_strtolower($contractStatusFilter);
            if (in_array($filterStatus, ['active', 'free'], true)) {
                $players = $players->filter(fn ($p) => mb_strtolower((string) ($p['contract_status'] ?? '')) === $filterStatus);
            }
        }

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

        $players = match ($sort) {
            'name_asc' => $players->sortBy(fn ($p) => mb_strtolower((string) ($p['name'] ?? ''))),
            'age_asc' => $players->sortBy(fn ($p) => (int) ($p['age'] ?? 999)),
            'age_desc' => $players->sortByDesc(fn ($p) => (int) ($p['age'] ?? 0)),
            'rating_asc' => $players->sortBy(fn ($p) => (float) ($p['rating'] ?? 0)),
            'value_desc' => $players->sortByDesc(fn ($p) => (float) ($p['market_value'] ?? 0)),
            'updated_desc' => $players->sortByDesc(fn ($p) => strtotime((string) ($p['updated_at'] ?? '1970-01-01'))),
            default => $players->sortByDesc(fn ($p) => (float) ($p['rating'] ?? 0)),
        };

        $total = $players->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $items = $players->slice(($page - 1) * $perPage, $perPage)->values()->all();

        return response()->json([
            'ok' => true,
            'data' => $items,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'sort' => $sort,
            ],
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
                'data_quality' => [
                    'score' => collect([
                        !empty($profile?->position),
                        !empty($profile?->bio),
                        !empty($card?->profile_photo_url),
                        (float) ($card?->overall_rating ?? 0) > 0 || (float) ($latestStat?->rating ?? 0) > 0,
                        (int) ($statsSummary['matches'] ?? 0) > 0,
                    ])->filter(fn ($v) => $v === true)->count() * 20,
                    'updated_at' => optional($profile?->updated_at ?? $card?->updated_at ?? $user->updated_at)->toIso8601String(),
                    'source' => 'NextScout API',
                ],
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerProfileCard;
use App\Models\ManagerProfileCard;
use App\Models\CoachProfileCard;
use App\Models\ProfileCardView;
use App\Models\ProfileCardInteraction;
use App\Models\ProfileCardSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileCardController extends Controller
{
    // Futbolcu Kartı Getir
    public function getPlayerCard(int $playerId): JsonResponse
    {
        $card = PlayerProfileCard::with('interactions')
            ->where('user_id', $playerId)
            ->firstOrFail();

        // Bakış kaydı
        ProfileCardView::create([
            'card_type' => 'player',
            'card_owner_user_id' => $playerId,
            'viewer_user_id' => auth()->user()->id,
            'viewed_at' => now(),
            'view_type' => 'full',
            'viewed_photos' => true,
            'viewed_videos' => true,
            'viewed_stats' => true,
        ]);

        // Görünüm sayısını artır
        $card->increment('viewers_count');

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $card->id,
                'user_id' => $card->user_id,
                'full_name' => $card->full_name,
                'age' => $card->age,
                'position' => $card->position,
                'sport' => $card->sport,
                'sport_level' => $card->sport_level,
                'height' => $card->height,
                'weight' => $card->weight,
                'preferred_foot' => $card->preferred_foot,

                'images' => [
                    'profile' => $card->profile_photo_url,
                    'banner' => $card->banner_photo_url,
                    'gallery' => $card->gallery_photos,
                ],

                'videos' => [
                    'main_highlight' => [
                        'url' => $card->main_video_url,
                        'duration' => $card->video_duration,
                    ],
                    'other_videos' => $card->other_videos,
                ],

                'statistics' => $card->getSportStats(),

                'engagement' => [
                    'views' => $card->viewers_count,
                    'favorites' => $card->favorites_count,
                    'likes' => $card->like_count,
                    'comments' => $card->comment_count,
                    'average_rating' => $card->average_rating,
                ],

                'social' => $card->social_links,
                'is_verified' => $card->is_verified,
            ],
        ]);
    }

    // Menajer Kartı Getir
    public function getManagerCard(int $managerId): JsonResponse
    {
        $card = ManagerProfileCard::where('user_id', $managerId)
            ->firstOrFail();

        ProfileCardView::create([
            'card_type' => 'manager',
            'card_owner_user_id' => $managerId,
            'viewer_user_id' => auth()->user()->id,
            'viewed_at' => now(),
            'view_type' => 'full',
            'viewed_photos' => true,
            'viewed_videos' => true,
            'viewed_stats' => true,
        ]);

        $card->increment('viewers_count');

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $card->id,
                'user_id' => $card->user_id,
                'full_name' => $card->full_name,
                'age' => $card->age,
                'current_team' => $card->current_team,
                'specialization' => $card->specialization,

                'images' => [
                    'profile' => $card->profile_photo_url,
                    'banner' => $card->banner_photo_url,
                    'gallery' => $card->gallery_photos,
                ],

                'videos' => [
                    'introduction' => $card->intro_video_url,
                    'coaching_sessions' => $card->coaching_videos,
                ],

                'experience' => [
                    'years' => $card->years_experience,
                    'teams_managed' => $card->teams_managed,
                    'players_developed' => $card->players_developed,
                    'win_rate' => $card->win_rate,
                ],

                'engagement' => [
                    'views' => $card->viewers_count,
                    'followers' => $card->followers_count,
                    'rating' => $card->overall_rating,
                ],

                'social' => $card->social_links,
                'is_verified' => $card->is_verified,
            ],
        ]);
    }

    // Antrenör Kartı Getir
    public function getCoachCard(int $coachId): JsonResponse
    {
        $card = CoachProfileCard::where('user_id', $coachId)
            ->firstOrFail();

        ProfileCardView::create([
            'card_type' => 'coach',
            'card_owner_user_id' => $coachId,
            'viewer_user_id' => auth()->user()->id,
            'viewed_at' => now(),
            'view_type' => 'full',
            'viewed_photos' => true,
            'viewed_videos' => true,
            'viewed_stats' => true,
        ]);

        $card->increment('viewers_count');

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $card->id,
                'user_id' => $card->user_id,
                'full_name' => $card->full_name,
                'age' => $card->age,
                'current_team' => $card->current_team,
                'coaching_area' => $card->coaching_area,
                'primary_sport' => $card->primary_sport,
                'sports' => $card->sports,
                'sports_experience' => $card->sports_experience,

                'images' => [
                    'profile' => $card->profile_photo_url,
                    'banner' => $card->banner_photo_url,
                    'gallery' => $card->gallery_photos,
                ],

                'videos' => [
                    'coaching_technique' => $card->coaching_technique_video,
                    'training_sessions' => $card->training_session_videos,
                ],

                'qualifications' => [
                    'certifications' => $card->certifications,
                    'languages' => $card->languages,
                ],

                'expertise' => [
                    'years_experience' => $card->years_experience,
                    'players_trained' => $card->players_trained,
                    'success_rate' => $card->success_rate,
                ],

                'engagement' => [
                    'views' => $card->viewers_count,
                    'followers' => $card->followers_count,
                    'rating' => $card->overall_rating,
                ],

                'social' => $card->social_links,
                'is_verified' => $card->is_verified,
            ],
        ]);
    }

    // Kardı Beğen
    public function likeCard(Request $request, string $cardType, int $cardOwnerId): JsonResponse
    {
        $validated = $request->validate([
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        ProfileCardInteraction::updateOrCreate(
            [
                'card_type' => $cardType,
                'card_owner_user_id' => $cardOwnerId,
                'user_id' => $request->user()->id,
                'interaction_type' => 'like',
            ],
            $validated
        );

        return response()->json([
            'ok' => true,
            'message' => 'Kartı beğendiniz!',
        ]);
    }

    // Kart Yorum
    public function commentCard(Request $request, string $cardType, int $cardOwnerId): JsonResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:500'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
        ]);

        ProfileCardInteraction::create([
            'card_type' => $cardType,
            'card_owner_user_id' => $cardOwnerId,
            'user_id' => $request->user()->id,
            'interaction_type' => 'comment',
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Yorum eklendi!',
        ]);
    }

    // Profil Kartı Ayarları Güncelle
    public function updateCardSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['nullable', 'in:light,dark,gradient,minimalist'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'layout' => ['nullable', 'in:modern,classic,artistic,minimal'],
            'show_social_links' => ['nullable', 'boolean'],
            'show_statistics' => ['nullable', 'boolean'],
            'show_video_highlight' => ['nullable', 'boolean'],
            'allow_messages' => ['nullable', 'boolean'],
            'show_view_count' => ['nullable', 'boolean'],
        ]);

        $settings = ProfileCardSettings::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return response()->json([
            'ok' => true,
            'message' => 'Kartı ayarları güncellendi!',
            'data' => $settings,
        ]);
    }

    // Kartı Yıldızla
    public function saveCard(Request $request, string $cardType, int $cardOwnerId): JsonResponse
    {
        $model = match($cardType) {
            'player' => PlayerProfileCard::where('user_id', $cardOwnerId)->first(),
            'manager' => ManagerProfileCard::where('user_id', $cardOwnerId)->first(),
            'coach' => CoachProfileCard::where('user_id', $cardOwnerId)->first(),
        };

        if (!$model) {
            return response()->json([
                'ok' => false,
                'message' => 'Kart bulunamadı.',
            ], 404);
        }

        ProfileCardInteraction::updateOrCreate(
            [
                'card_type' => $cardType,
                'card_owner_user_id' => $cardOwnerId,
                'user_id' => $request->user()->id,
                'interaction_type' => 'save',
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Kartı kaydettiniz!',
        ]);
    }

    // Kart Görüntüleme İstatistikleri
    public function getCardStats(string $cardType, int $cardOwnerId): JsonResponse
    {
        $views = ProfileCardView::where('card_type', $cardType)
            ->where('card_owner_user_id', $cardOwnerId)
            ->count();

        $interactions = ProfileCardInteraction::where('card_type', $cardType)
            ->where('card_owner_user_id', $cardOwnerId)
            ->get()
            ->groupBy('interaction_type')
            ->map(fn($items) => $items->count());

        return response()->json([
            'ok' => true,
            'data' => [
                'total_views' => $views,
                'likes' => $interactions->get('like', 0),
                'comments' => $interactions->get('comment', 0),
                'saves' => $interactions->get('save', 0),
                'shares' => $interactions->get('share', 0),
            ],
        ]);
    }
}

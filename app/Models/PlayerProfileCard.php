<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerProfileCard extends Model
{
    protected $table = 'player_profile_card';

    protected $fillable = [
        'user_id', 'full_name', 'age', 'position', 'sport', 'sport_level', 'height', 'weight',
        'preferred_foot', 'profile_photo_url', 'banner_photo_url',
        'gallery_photos', 'main_video_url', 'video_duration', 'other_videos',
        'goals', 'assists', 'matches_played',
        'basketball_points', 'basketball_rebounds', 'basketball_assists',
        'volleyball_kills', 'volleyball_blocks', 'volleyball_aces',
        'overall_rating', 'viewers_count', 'favorites_count', 'social_links',
        'is_public', 'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'gallery_photos' => 'array',
            'other_videos' => 'array',
            'social_links' => 'array',
            'overall_rating' => 'decimal:1',
            'is_public' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    // Spor Bazlı İstatistikleri Getir
    public function getSportStats(): array
    {
        return match($this->sport) {
            'football' => [
                'goals' => $this->goals,
                'assists' => $this->assists,
                'matches_played' => $this->matches_played,
            ],
            'basketball' => [
                'points' => $this->basketball_points,
                'rebounds' => $this->basketball_rebounds,
                'assists' => $this->basketball_assists,
            ],
            'volleyball' => [
                'kills' => $this->volleyball_kills,
                'blocks' => $this->volleyball_blocks,
                'aces' => $this->volleyball_aces,
            ],
            default => [],
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ProfileCardView::class, 'card_owner_user_id');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(ProfileCardInteraction::class, 'card_owner_user_id');
    }

    public function getLikeCountAttribute(): int
    {
        return $this->interactions()
            ->where('interaction_type', 'like')
            ->count();
    }

    public function getCommentCountAttribute(): int
    {
        return $this->interactions()
            ->where('interaction_type', 'comment')
            ->count();
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->interactions()
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;
    }
}

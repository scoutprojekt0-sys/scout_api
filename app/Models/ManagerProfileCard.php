<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagerProfileCard extends Model
{
    protected $table = 'manager_profile_card';

    protected $fillable = [
        'user_id', 'full_name', 'age', 'current_team', 'specialization',
        'profile_photo_url', 'banner_photo_url', 'gallery_photos',
        'intro_video_url', 'coaching_videos', 'years_experience',
        'teams_managed', 'players_developed', 'win_rate', 'overall_rating',
        'viewers_count', 'followers_count', 'social_links',
        'is_public', 'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'gallery_photos' => 'array',
            'coaching_videos' => 'array',
            'win_rate' => 'decimal:2',
            'overall_rating' => 'decimal:1',
            'social_links' => 'array',
            'is_public' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(ProfileCardInteraction::class, 'card_owner_user_id');
    }
}

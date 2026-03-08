<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoachProfileCard extends Model
{
    protected $table = 'coach_profile_card';

    protected $fillable = [
        'user_id', 'full_name', 'age', 'current_team', 'coaching_area',
        'sports', 'primary_sport', 'sports_experience',
        'certifications', 'languages', 'profile_photo_url', 'banner_photo_url',
        'gallery_photos', 'coaching_technique_video', 'training_session_videos',
        'years_experience', 'players_trained', 'success_rate', 'overall_rating',
        'viewers_count', 'followers_count', 'social_links',
        'is_public', 'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'certifications' => 'array',
            'languages' => 'array',
            'gallery_photos' => 'array',
            'training_session_videos' => 'array',
            'sports' => 'array',
            'sports_experience' => 'array',
            'success_rate' => 'decimal:2',
            'overall_rating' => 'decimal:1',
            'social_links' => 'array',
            'is_public' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    // ...existing code...

    public function interactions(): HasMany
    {
        return $this->hasMany(ProfileCardInteraction::class, 'card_owner_user_id');
    }

    // Sporlar Bilgisini Getir
    public function getSportsInfo(): array
    {
        return [
            'sports' => $this->sports ?? ['football'],
            'primary_sport' => $this->primary_sport ?? 'football',
            'experience' => $this->sports_experience ?? [],
        ];
    }

    // Belirli Spordaki Deneyimi Getir
    public function getSportExperience(string $sport): ?array
    {
        return $this->sports_experience[$sport] ?? null;
    }
}

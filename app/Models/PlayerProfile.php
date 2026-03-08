<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerProfile extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id', 'birth_year', 'date_of_birth', 'place_of_birth',
        'position', 'primary_position_id', 'secondary_positions',
        'dominant_foot', 'preferred_foot', 'height_cm', 'weight_kg',
        'body_type', 'bio', 'current_team', 'current_club_id',
        'nationality_id', 'second_nationalities', 'youth_club_id',
        'current_market_value', 'highest_market_value', 'contract_expires',
        'agent_name', 'instagram_handle', 'twitter_handle', 'social_followers',
        'languages', 'jersey_number', 'is_retired', 'retirement_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_year' => 'integer',
            'date_of_birth' => 'date',
            'height_cm' => 'integer',
            'weight_kg' => 'integer',
            'secondary_positions' => 'array',
            'second_nationalities' => 'array',
            'languages' => 'array',
            'current_market_value' => 'decimal:2',
            'highest_market_value' => 'decimal:2',
            'contract_expires' => 'date',
            'social_followers' => 'integer',
            'is_retired' => 'boolean',
            'retirement_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'current_club_id');
    }

    public function primaryPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'primary_position_id');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function youthClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'youth_club_id');
    }

    // Helper method - Yaş hesaplama
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth
            ? now()->diffInYears($this->date_of_birth)
            : null;
    }
}

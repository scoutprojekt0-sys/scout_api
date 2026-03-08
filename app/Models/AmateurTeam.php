<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmateurTeam extends Model
{
    protected $fillable = [
        'user_id', 'team_name', 'team_type', 'city', 'district',
        'neighborhood', 'description', 'home_field', 'field_type',
        'practice_days', 'practice_time', 'current_players',
        'needed_players', 'needed_positions', 'monthly_fee',
        'accepts_new_players', 'contact_phone', 'whatsapp_group',
    ];

    protected function casts(): array
    {
        return [
            'needed_positions' => 'array',
            'current_players' => 'integer',
            'needed_players' => 'integer',
            'monthly_fee' => 'decimal:2',
            'accepts_new_players' => 'boolean',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function playerSearches(): HasMany
    {
        return $this->hasMany(TeamPlayerSearch::class, 'team_id');
    }

    public function trialRequests(): HasMany
    {
        return $this->hasMany(TrialRequest::class, 'team_id');
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(AmateurMatchRecord::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(AmateurMatchRecord::class, 'away_team_id');
    }
}

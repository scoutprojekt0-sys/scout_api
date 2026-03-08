<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchDetail extends Model
{
    protected $fillable = [
        'league_id', 'season_id', 'home_club_id', 'away_club_id',
        'match_date', 'venue', 'home_score', 'away_score',
        'home_halftime_score', 'away_halftime_score', 'match_type',
        'status', 'round', 'attendance', 'referee', 'match_report',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'home_score' => 'integer',
            'away_score' => 'integer',
            'home_halftime_score' => 'integer',
            'away_halftime_score' => 'integer',
            'round' => 'integer',
            'attendance' => 'integer',
        ];
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    public function playerStats(): HasMany
    {
        return $this->hasMany(MatchPlayerStat::class, 'match_id');
    }
}

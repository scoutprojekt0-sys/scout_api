<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSeasonStatistic extends Model
{
    protected $fillable = [
        'team_id', 'season_id', 'matches_played', 'matches_won',
        'matches_drawn', 'matches_lost', 'goals_for', 'goals_against',
        'goal_difference', 'points', 'total_players', 'injured_players',
        'recent_form', 'last_match_date',
    ];

    protected function casts(): array
    {
        return [
            'last_match_date' => 'date',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'team_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function getWinRateAttribute(): float
    {
        return $this->matches_played > 0
            ? round(($this->matches_won / $this->matches_played) * 100, 2)
            : 0;
    }
}

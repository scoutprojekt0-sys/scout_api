<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeagueStanding extends Model
{
    protected $fillable = [
        'league_id', 'season_id', 'club_id', 'position', 'played',
        'won', 'drawn', 'lost', 'goals_for', 'goals_against',
        'goal_difference', 'points', 'form',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'played' => 'integer',
            'won' => 'integer',
            'drawn' => 'integer',
            'lost' => 'integer',
            'goals_for' => 'integer',
            'goals_against' => 'integer',
            'goal_difference' => 'integer',
            'points' => 'integer',
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

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}

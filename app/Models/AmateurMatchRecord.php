<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmateurMatchRecord extends Model
{
    protected $fillable = [
        'league_id', 'home_team_id', 'away_team_id', 'match_date',
        'venue', 'home_score', 'away_score', 'match_type',
        'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'date',
            'home_score' => 'integer',
            'away_score' => 'integer',
        ];
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(AmateurLeague::class, 'league_id');
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'away_team_id');
    }
}

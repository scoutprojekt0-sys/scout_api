<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMatchSchedule extends Model
{
    protected $fillable = [
        'team_id', 'season_id', 'week', 'match_week_start',
        'match_week_end', 'matches_scheduled', 'matches_completed',
        'matches_pending', 'team_status',
    ];

    protected function casts(): array
    {
        return [
            'match_week_start' => 'date',
            'match_week_end' => 'date',
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
}

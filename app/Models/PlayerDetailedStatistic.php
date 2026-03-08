<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerDetailedStatistic extends Model
{
    protected $fillable = [
        'player_user_id', 'club_id', 'season_id', 'league_id',
        'appearances', 'starts', 'substitutions_on', 'substitutions_off',
        'minutes_played', 'goals', 'assists', 'penalties_scored',
        'penalties_missed', 'own_goals', 'yellow_cards', 'red_cards',
        'second_yellow_cards', 'shots_on_target', 'shots_off_target',
        'shot_accuracy', 'passes_completed', 'passes_attempted',
        'pass_accuracy', 'key_passes', 'tackles', 'interceptions',
        'clearances', 'blocks', 'aerial_duels_won', 'aerial_duels_lost',
        'dribbles_completed', 'dribbles_attempted', 'saves',
        'clean_sheets', 'goals_conceded', 'penalties_saved',
        'average_rating', 'man_of_the_match',
    ];

    protected function casts(): array
    {
        return [
            'shot_accuracy' => 'decimal:2',
            'pass_accuracy' => 'decimal:2',
            'average_rating' => 'decimal:1',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMatchPerformance extends Model
{
    protected $fillable = [
        'match_id', 'player_user_id', 'team_id', 'played',
        'goals', 'assists', 'yellow_cards', 'red_cards',
        'rating', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'played' => 'boolean',
            'goals' => 'integer',
            'assists' => 'integer',
            'yellow_cards' => 'integer',
            'red_cards' => 'integer',
            'rating' => 'integer',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(AmateurMatchRecord::class, 'match_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'team_id');
    }
}

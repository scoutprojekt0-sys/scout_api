<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchPlayerStat extends Model
{
    protected $fillable = [
        'match_id', 'player_user_id', 'club_id', 'position_id',
        'shirt_number', 'is_starter', 'minutes_played',
        'substituted_in_minute', 'substituted_out_minute',
        'goals', 'assists', 'yellow_cards', 'red_cards',
        'own_goal', 'rating', 'man_of_the_match',
    ];

    protected function casts(): array
    {
        return [
            'is_starter' => 'boolean',
            'own_goal' => 'boolean',
            'man_of_the_match' => 'boolean',
            'rating' => 'decimal:1',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchDetail::class, 'match_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}

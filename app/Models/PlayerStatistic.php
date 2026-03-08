<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStatistic extends Model
{
    protected $fillable = [
        'player_user_id',
        'season',
        'competition',
        'matches_played',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'minutes_played',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'matches_played' => 'integer',
            'goals' => 'integer',
            'assists' => 'integer',
            'yellow_cards' => 'integer',
            'red_cards' => 'integer',
            'minutes_played' => 'integer',
            'rating' => 'decimal:1',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveMatch extends Model
{
    protected $table = 'live_matches';

    protected $fillable = [
        'title',
        'home_team',
        'away_team',
        'match_date',
        'home_score',
        'away_score',
        'is_live',
        'is_finished',
        'league',
        'round',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'is_live' => 'boolean',
            'is_finished' => 'boolean',
        ];
    }
}

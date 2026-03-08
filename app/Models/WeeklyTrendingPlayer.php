<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyTrendingPlayer extends Model
{
    protected $fillable = [
        'player_id', 'weekly_points', 'weekly_views', 'weekly_rank', 'week_start', 'week_end',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
        ];
    }
}

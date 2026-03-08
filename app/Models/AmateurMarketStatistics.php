<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmateurMarketStatistics extends Model
{
    protected $fillable = [
        'total_players', 'active_players', 'average_market_value', 'highest_value',
        'lowest_value', 'trending_up_count', 'trending_down_count', 'stable_count',
        'daily_profile_views', 'daily_likes', 'daily_comments', 'statistics_date',
    ];

    protected function casts(): array
    {
        return [
            'statistics_date' => 'date',
            'average_market_value' => 'decimal:2',
        ];
    }
}

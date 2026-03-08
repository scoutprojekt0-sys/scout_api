<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SportSpecificStat extends Model
{
    protected $fillable = [
        'player_user_id', 'sport',
        'football_goals', 'football_assists',
        'basketball_points', 'basketball_rebounds', 'basketball_assists', 'basketball_steals',
        'volleyball_aces', 'volleyball_kills', 'volleyball_blocks', 'volleyball_digs',
    ];

    protected function casts(): array
    {
        return [
            'football_goals' => 'integer',
            'football_assists' => 'integer',
            'basketball_points' => 'integer',
            'basketball_rebounds' => 'integer',
            'basketball_assists' => 'integer',
            'basketball_steals' => 'integer',
            'volleyball_aces' => 'integer',
            'volleyball_kills' => 'integer',
            'volleyball_blocks' => 'integer',
            'volleyball_digs' => 'integer',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

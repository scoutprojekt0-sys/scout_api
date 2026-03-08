<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Injury extends Model
{
    protected $fillable = [
        'player_user_id', 'injury_type', 'severity', 'injury_date',
        'expected_return_date', 'actual_return_date', 'days_out',
        'games_missed', 'description', 'status',
    ];

    protected function casts(): array
    {
        return [
            'injury_date' => 'date',
            'expected_return_date' => 'date',
            'actual_return_date' => 'date',
            'days_out' => 'integer',
            'games_missed' => 'integer',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

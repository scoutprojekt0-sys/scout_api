<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NationalTeamPlayer extends Model
{
    protected $fillable = [
        'player_user_id', 'country_id', 'team_type',
        'caps', 'goals', 'debut_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'caps' => 'integer',
            'goals' => 'integer',
            'debut_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

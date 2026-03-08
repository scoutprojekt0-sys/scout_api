<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPlayerAvailability extends Model
{
    protected $fillable = [
        'team_id', 'total_squad_size', 'available_players',
        'injured_players', 'suspended_players', 'goalkeeper_count',
        'defender_count', 'midfielder_count', 'forward_count', 'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'last_updated' => 'date',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'team_id');
    }
}

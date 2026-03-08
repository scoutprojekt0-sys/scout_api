<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmateurTransferOffer extends Model
{
    protected $fillable = [
        'player_id', 'from_team_id', 'offer_message', 'proposed_value',
        'status', 'expires_at', 'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function fromTeam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_team_id');
    }
}

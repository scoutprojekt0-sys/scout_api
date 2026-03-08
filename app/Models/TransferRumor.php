<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferRumor extends Model
{
    protected $fillable = [
        'player_user_id', 'from_club_id', 'to_club_id', 'source',
        'reliability', 'estimated_fee', 'description', 'status', 'rumor_date',
    ];

    protected function casts(): array
    {
        return [
            'estimated_fee' => 'decimal:2',
            'rumor_date' => 'date',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function fromClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'from_club_id');
    }

    public function toClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'to_club_id');
    }
}

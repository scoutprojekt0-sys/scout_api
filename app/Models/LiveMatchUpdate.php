<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveMatchUpdate extends Model
{
    protected $fillable = [
        'match_id', 'update_time', 'status', 'home_score',
        'away_score', 'current_minute', 'events', 'match_commentary', 'possession',
    ];

    protected function casts(): array
    {
        return [
            'update_time' => 'datetime',
            'events' => 'array',
            'possession' => 'array',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(AmateurMatchRecord::class, 'match_id');
    }
}

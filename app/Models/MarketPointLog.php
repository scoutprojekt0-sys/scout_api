<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketPointLog extends Model
{
    protected $fillable = [
        'player_id', 'action_type', 'points_gained', 'description', 'related_id', 'running_total',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}

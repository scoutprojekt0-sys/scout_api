<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMarketValue extends Model
{
    protected $fillable = [
        'player_user_id', 'market_value', 'valuation_date',
        'currency', 'change_reason',
    ];

    protected function casts(): array
    {
        return [
            'market_value' => 'decimal:2',
            'valuation_date' => 'date',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

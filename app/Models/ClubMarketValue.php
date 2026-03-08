<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubMarketValue extends Model
{
    protected $fillable = [
        'club_id', 'total_market_value', 'average_market_value',
        'valuation_date', 'squad_size',
    ];

    protected function casts(): array
    {
        return [
            'total_market_value' => 'decimal:2',
            'average_market_value' => 'decimal:2',
            'valuation_date' => 'date',
            'squad_size' => 'integer',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}

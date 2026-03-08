<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'player_user_id', 'from_club_id', 'to_club_id', 'season_id',
        'transfer_date', 'transfer_type', 'transfer_fee', 'currency',
        'market_value_at_time', 'notes', 'is_confirmed',
        'loan_end_date', 'option_to_buy',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
            'loan_end_date' => 'date',
            'transfer_fee' => 'decimal:2',
            'market_value_at_time' => 'decimal:2',
            'is_confirmed' => 'boolean',
            'option_to_buy' => 'boolean',
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

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}

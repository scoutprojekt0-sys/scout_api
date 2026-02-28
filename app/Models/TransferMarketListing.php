<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferMarketListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_user_id',
        'asking_fee_eur',
        'salary_min_eur',
        'salary_max_eur',
        'contract_until',
        'form_score',
        'minutes_5_matches',
        'injury_status',
        'market_status',
        'note',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

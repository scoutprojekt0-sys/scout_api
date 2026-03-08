<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractNegotiation extends Model
{
    protected $fillable = [
        'contract_id', 'lawyer_id', 'stage', 'player_request',
        'manager_offer', 'lawyer_recommendation', 'disputed_clauses',
        'amendments', 'proposed_at', 'reviewed_at', 'result',
    ];

    protected function casts(): array
    {
        return [
            'disputed_clauses' => 'array',
            'amendments' => 'array',
            'proposed_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractDispute extends Model
{
    protected $fillable = [
        'contract_id', 'raised_by', 'user_id', 'title', 'description',
        'severity', 'related_clauses', 'status', 'resolution', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'related_clauses' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

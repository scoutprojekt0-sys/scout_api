<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'player_user_id', 'manager_user_id', 'lawyer_id',
        'contract_number', 'type', 'start_date', 'end_date', 'contract_date',
        'total_amount', 'payment_schedule', 'terms_conditions', 'clauses',
        'special_conditions', 'status', 'player_signed_at', 'manager_signed_at',
        'lawyer_approved_at', 'documents', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'contract_date' => 'date',
            'total_amount' => 'decimal:2',
            'payment_schedule' => 'array',
            'clauses' => 'array',
            'special_conditions' => 'array',
            'documents' => 'array',
            'player_signed_at' => 'datetime',
            'manager_signed_at' => 'datetime',
            'lawyer_approved_at' => 'datetime',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function negotiations(): HasMany
    {
        return $this->hasMany(ContractNegotiation::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ContractVersion::class);
    }

    public function signatureRequests(): HasMany
    {
        return $this->hasMany(SignatureRequest::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(ContractDispute::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(LawyerReview::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(ContractHistory::class);
    }

    public function getProgressAttribute(): int
    {
        return match($this->status) {
            'draft' => 10,
            'proposed' => 25,
            'under_negotiation' => 40,
            'awaiting_signature' => 75,
            'signed' => 90,
            'active' => 100,
            'completed' => 100,
            default => 0,
        };
    }
}

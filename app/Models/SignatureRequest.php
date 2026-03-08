<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatureRequest extends Model
{
    protected $fillable = [
        'contract_id', 'requested_from', 'user_id', 'lawyer_id',
        'request_message', 'requested_at', 'deadline', 'status',
        'signed_at', 'rejection_reason', 'signature_ip', 'signature_device',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'deadline' => 'datetime',
            'signed_at' => 'datetime',
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

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lawyer extends Model
{
    protected $fillable = [
        'user_id', 'license_number', 'specialization', 'bio',
        'office_name', 'office_address', 'office_phone', 'office_email',
        'years_experience', 'past_clients', 'hourly_rate', 'contract_fee',
        'is_verified', 'is_active', 'license_status',
    ];

    protected function casts(): array
    {
        return [
            'past_clients' => 'array',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function negotiations(): HasMany
    {
        return $this->hasMany(ContractNegotiation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(LawyerReview::class);
    }
}

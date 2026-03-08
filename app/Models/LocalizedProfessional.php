<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalizedProfessional extends Model
{
    protected $fillable = [
        'lawyer_id', 'country_id', 'local_license_number', 'local_bar_association',
        'languages_spoken', 'regions_covered', 'sports_specialized', 'is_verified_locally',
    ];

    protected function casts(): array
    {
        return [
            'languages_spoken' => 'array',
            'regions_covered' => 'array',
            'sports_specialized' => 'array',
            'is_verified_locally' => 'boolean',
        ];
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

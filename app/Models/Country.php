<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'code', 'name', 'currency_code', 'currency_symbol', 'currency_rate',
        'popular_sports', 'supported_sports', 'default_language', 'supported_languages',
        'legal_system', 'labor_law_type', 'timezone', 'region', 'cities',
        'is_active', 'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'popular_sports' => 'array',
            'supported_sports' => 'array',
            'supported_languages' => 'array',
            'cities' => 'array',
            'currency_rate' => 'decimal:4',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function legalRequirements(): HasMany
    {
        return $this->hasMany(LegalRequirementsByCountry::class);
    }

    public function sportRules(): HasMany
    {
        return $this->hasMany(SportRulesByCountry::class);
    }

    public function localizedProfessionals(): HasMany
    {
        return $this->hasMany(LocalizedProfessional::class);
    }

    public function localizedContent(): HasMany
    {
        return $this->hasMany(LocalizedContent::class);
    }
}

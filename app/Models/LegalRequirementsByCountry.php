<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalRequirementsByCountry extends Model
{
    protected $fillable = [
        'country_id', 'required_documents', 'contract_template', 'mandatory_clauses',
        'forbidden_clauses', 'minimum_salary', 'salary_period', 'income_tax_rate',
        'social_security_rate', 'max_weekly_hours', 'min_rest_days_per_week',
        'annual_leave_days', 'public_holidays', 'min_contract_duration',
        'max_contract_duration', 'notice_period_days', 'severance_multiplier',
        'min_age_to_play', 'requires_parental_consent', 'requires_sport_license',
        'sport_license_issuer', 'special_regulations',
    ];

    protected function casts(): array
    {
        return [
            'required_documents' => 'array',
            'mandatory_clauses' => 'array',
            'forbidden_clauses' => 'array',
            'minimum_salary' => 'decimal:2',
            'income_tax_rate' => 'decimal:2',
            'social_security_rate' => 'decimal:2',
            'severance_multiplier' => 'decimal:2',
            'requires_parental_consent' => 'boolean',
            'requires_sport_license' => 'boolean',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LawyerReview extends Model
{
    protected $fillable = [
        'contract_id', 'lawyer_id', 'legal_review', 'risk_assessment',
        'recommendations', 'compliance_score', 'concerns', 'required_changes',
        'review_status',
    ];

    protected function casts(): array
    {
        return [
            'risk_assessment' => 'array',
            'recommendations' => 'array',
            'concerns' => 'array',
            'required_changes' => 'array',
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

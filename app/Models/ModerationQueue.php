<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationQueue extends Model
{
    use HasFactory;

    protected $table = 'moderation_queue';

    protected $fillable = [
        'model_type',
        'model_id',
        'status',
        'priority',
        'reason',
        'proposed_changes',
        'current_values',
        'change_description',
        'source_url',
        'confidence_score',
        'submitted_by',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
        'requires_dual_approval',
        'second_reviewer_id',
        'second_review_at',
    ];

    protected $casts = [
        'proposed_changes' => 'array',
        'current_values' => 'array',
        'confidence_score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'second_review_at' => 'datetime',
        'requires_dual_approval' => 'boolean',
    ];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function secondReviewer()
    {
        return $this->belongsTo(User::class, 'second_reviewer_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high')
                    ->orWhere('priority', 'critical');
    }

    public function scopeOldest($query)
    {
        return $query->orderBy('submitted_at', 'asc');
    }

    public function approve(int $reviewerId, ?string $notes = null): bool
    {
        if ($this->requires_dual_approval && !$this->second_reviewer_id) {
            $this->update([
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
                'reviewer_notes' => $notes,
            ]);

            return false; // Still needs second approval
        }

        $this->update([
            'status' => 'approved',
            $this->second_reviewer_id ? 'second_reviewer_id' : 'reviewed_by' => $reviewerId,
            $this->second_reviewer_id ? 'second_review_at' : 'reviewed_at' => now(),
            'reviewer_notes' => $notes,
        ]);

        return true;
    }

    public function reject(int $reviewerId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'reviewer_notes' => $reason,
        ]);
    }

    public function flag(int $reviewerId, string $reason): void
    {
        $this->update([
            'status' => 'flagged',
            'priority' => 'high',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'reviewer_notes' => $reason,
        ]);
    }
}

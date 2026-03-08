<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialRequest extends Model
{
    protected $fillable = [
        'player_user_id', 'team_id', 'request_type', 'message',
        'preferred_date', 'preferred_time', 'status', 'team_response',
        'scheduled_date', 'feedback', 'performance_rating', 'offered_position',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'scheduled_date' => 'date',
            'performance_rating' => 'integer',
            'offered_position' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'team_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoutReport extends Model
{
    protected $fillable = [
        'scout_user_id',
        'player_user_id',
        'title',
        'technical_assessment',
        'physical_assessment',
        'mental_assessment',
        'overall_rating',
        'recommendation',
        'watched_date',
        'watched_location',
        'is_private',
    ];

    protected function casts(): array
    {
        return [
            'overall_rating' => 'integer',
            'is_private' => 'boolean',
            'watched_date' => 'date',
        ];
    }

    public function scout(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scout_user_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

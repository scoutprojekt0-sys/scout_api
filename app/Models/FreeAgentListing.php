<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeAgentListing extends Model
{
    protected $fillable = [
        'player_user_id', 'title', 'preferred_positions', 'city',
        'district', 'availability', 'available_days', 'available_time',
        'skill_level', 'max_monthly_fee', 'has_equipment',
        'has_transportation', 'about', 'experience', 'status',
    ];

    protected function casts(): array
    {
        return [
            'preferred_positions' => 'array',
            'available_days' => 'array',
            'max_monthly_fee' => 'decimal:2',
            'has_equipment' => 'boolean',
            'has_transportation' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

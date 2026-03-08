<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPlayerSearch extends Model
{
    protected $fillable = [
        'team_id', 'posted_by', 'title', 'positions_needed',
        'players_needed', 'min_age', 'max_age', 'skill_level',
        'requirements', 'monthly_fee', 'transportation_provided',
        'equipment_provided', 'commitment_level', 'status',
    ];

    protected function casts(): array
    {
        return [
            'positions_needed' => 'array',
            'players_needed' => 'integer',
            'min_age' => 'integer',
            'max_age' => 'integer',
            'monthly_fee' => 'decimal:2',
            'transportation_provided' => 'boolean',
            'equipment_provided' => 'boolean',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'team_id');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}

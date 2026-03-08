<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPerformanceTrend extends Model
{
    protected $fillable = [
        'team_id', 'calculation_date', 'average_goals_per_match',
        'average_goals_against', 'win_percentage', 'clean_sheets_percentage',
        'trend', 'performance_notes',
    ];

    protected function casts(): array
    {
        return [
            'calculation_date' => 'date',
            'average_goals_per_match' => 'decimal:2',
            'average_goals_against' => 'decimal:2',
            'win_percentage' => 'decimal:2',
            'clean_sheets_percentage' => 'decimal:2',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(AmateurTeam::class, 'team_id');
    }
}

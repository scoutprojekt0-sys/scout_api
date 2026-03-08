<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SportRulesByCountry extends Model
{
    protected $fillable = [
        'country_id', 'sport', 'top_league_name', 'num_teams_in_top_league',
        'min_age', 'max_age', 'allows_foreign_players', 'max_foreign_players',
        'transfer_window_type', 'transfer_windows', 'has_salary_cap',
        'salary_cap_amount', 'foreign_player_restrictions',
    ];

    protected function casts(): array
    {
        return [
            'transfer_windows' => 'array',
            'allows_foreign_players' => 'boolean',
            'has_salary_cap' => 'boolean',
            'salary_cap_amount' => 'decimal:2',
            'foreign_player_restrictions' => 'array',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    protected $fillable = [
        'country_id', 'name', 'short_name', 'tier',
        'logo_url', 'is_active', 'team_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'team_count' => 'integer',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchDetail::class);
    }

    public function standings(): HasMany
    {
        return $this->hasMany(LeagueStanding::class);
    }
}

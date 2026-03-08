<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'country_id', 'league_id', 'name', 'short_name', 'nickname',
        'logo_url', 'stadium_name', 'stadium_capacity', 'city',
        'founded_year', 'club_colors', 'description', 'website_url',
        'total_market_value', 'national_team_players', 'average_age',
        'foreigner_count',
    ];

    protected function casts(): array
    {
        return [
            'founded_year' => 'integer',
            'stadium_capacity' => 'integer',
            'total_market_value' => 'decimal:2',
            'average_age' => 'decimal:2',
            'national_team_players' => 'integer',
            'foreigner_count' => 'integer',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(PlayerProfile::class, 'current_club_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_club_id');
    }

    public function marketValues(): HasMany
    {
        return $this->hasMany(ClubMarketValue::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchDetail::class, 'home_club_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(MatchDetail::class, 'away_club_id');
    }

    public function standings(): HasMany
    {
        return $this->hasMany(LeagueStanding::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_current'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(PlayerDetailedStatistic::class);
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

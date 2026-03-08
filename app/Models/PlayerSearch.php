<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerSearch extends Model
{
    protected $fillable = [
        'manager_id', 'sport', 'position', 'gender', 'min_age', 'max_age',
        'min_height', 'max_height', 'skill_levels', 'locations',
        'min_rating', 'min_goals', 'min_matches', 'is_active', 'is_saved',
    ];

    protected function casts(): array
    {
        return [
            'skill_levels' => 'array',
            'locations' => 'array',
            'min_rating' => 'decimal:1',
            'is_active' => 'boolean',
            'is_saved' => 'boolean',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PlayerSearchResult::class, 'search_id');
    }
}

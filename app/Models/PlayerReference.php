<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerReference extends Model
{
    protected $fillable = [
        'player_user_id', 'given_by', 'reference_type', 'referee_name',
        'referee_position', 'reference_text', 'rating',
        'is_verified', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'given_by');
    }
}

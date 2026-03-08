<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecretInterestNotification extends Model
{
    protected $fillable = [
        'player_user_id', 'title', 'message', 'icon',
        'hint_location', 'hint_level', 'hint_position',
        'is_read', 'read_at', 'is_mystery', 'mystery_level',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'is_mystery' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }
}

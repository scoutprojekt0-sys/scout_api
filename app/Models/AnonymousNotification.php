<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnonymousNotification extends Model
{
    protected $fillable = [
        'player_user_id', 'triggered_by_user_id', 'notification_type',
        'message', 'emoji', 'metadata', 'is_read', 'read_at', 'hint',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }
}

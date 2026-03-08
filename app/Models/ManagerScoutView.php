<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerScoutView extends Model
{
    protected $fillable = [
        'player_user_id', 'manager_scout_id', 'view_time',
        'view_type', 'duration_seconds', 'is_anonymous',
        'viewer_display_name', 'notification_sent', 'notification_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'view_time' => 'datetime',
            'notification_sent_at' => 'datetime',
            'is_anonymous' => 'boolean',
            'notification_sent' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_scout_id');
    }
}

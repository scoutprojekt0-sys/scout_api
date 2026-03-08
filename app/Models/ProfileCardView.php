<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileCardView extends Model
{
    protected $fillable = [
        'card_type', 'card_owner_user_id', 'viewer_user_id',
        'viewed_at', 'view_duration_seconds', 'view_type',
        'viewed_photos', 'viewed_videos', 'viewed_stats',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'viewed_photos' => 'boolean',
            'viewed_videos' => 'boolean',
            'viewed_stats' => 'boolean',
        ];
    }

    public function cardOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'card_owner_user_id');
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_user_id');
    }
}

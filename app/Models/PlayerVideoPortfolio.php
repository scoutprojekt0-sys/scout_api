<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerVideoPortfolio extends Model
{
    protected $table = 'player_video_portfolio';

    protected $fillable = [
        'player_user_id', 'title', 'description', 'video_url',
        'thumbnail_url', 'video_type', 'recorded_date',
        'views', 'likes', 'is_featured', 'is_public',
    ];

    protected function casts(): array
    {
        return [
            'recorded_date' => 'date',
            'views' => 'integer',
            'likes' => 'integer',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function toggleLike(): void
    {
        $this->increment('likes');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileCardSettings extends Model
{
    protected $fillable = [
        'user_id', 'theme', 'primary_color', 'secondary_color',
        'layout', 'show_social_links', 'show_statistics',
        'show_video_highlight', 'allow_messages', 'show_view_count',
    ];

    protected function casts(): array
    {
        return [
            'show_social_links' => 'boolean',
            'show_statistics' => 'boolean',
            'show_video_highlight' => 'boolean',
            'allow_messages' => 'boolean',
            'show_view_count' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

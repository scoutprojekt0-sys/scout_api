<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'filename',
        'original_path',
        'cdn_url',
        'thumbnail_url',
        'transcoded_urls',
        'duration_seconds',
        'file_size',
        'mime_type',
        'status',
        'views_count',
        'likes_count',
        'visibility',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'transcoded_urls' => 'array',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}

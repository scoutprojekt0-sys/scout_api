<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'type',
        'points',
        'criteria',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['earned_at', 'progress', 'metadata'])
            ->withTimestamps();
    }
}

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'type',
        'media',
        'poll_data',
        'shared_post_id',
        'visibility',
        'is_pinned',
        'likes_count',
        'comments_count',
        'shares_count',
        'views_count',
    ];

    protected $casts = [
        'media' => 'array',
        'poll_data' => 'array',
        'is_pinned' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(CommunityPostLike::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(CommunityPostComment::class, 'post_id');
    }

    public function sharedPost(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'shared_post_id');
    }
}

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}

class Video extends Model
{
    use HasFactory;

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

    protected $casts = [
        'transcoded_urls' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}

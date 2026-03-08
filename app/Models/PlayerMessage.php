<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMessage extends Model
{
    protected $fillable = [
        'from_user_id', 'to_user_id', 'subject', 'message',
        'type', 'is_read', 'read_at', 'is_anonymous',
        'anonymous_name', 'attachments', 'archived_by_sender',
        'archived_by_recipient',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'is_anonymous' => 'boolean',
            'archived_by_sender' => 'boolean',
            'archived_by_recipient' => 'boolean',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function getSenderNameAttribute(): string
    {
        return $this->is_anonymous
            ? ($this->anonymous_name ?? 'Gizli Menajeri ⭐')
            : $this->sender->name;
    }
}

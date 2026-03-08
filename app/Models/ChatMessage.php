<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    protected $fillable = [
        'room_id', 'sender_id', 'message', 'attachments',
        'reactions', 'is_deleted', 'deleted_at', 'is_edited', 'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'reactions' => 'array',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
            'is_edited' => 'boolean',
            'edited_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(PlayerChatRoom::class, 'room_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id');
    }
}

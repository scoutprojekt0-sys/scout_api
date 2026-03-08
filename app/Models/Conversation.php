<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_1_id', 'user_2_id', 'last_message', 'last_message_at',
        'user_1_read', 'user_2_read',
    ];

    protected function casts(): array
    {
        return [
            'user_1_read' => 'boolean',
            'user_2_read' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_2_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getOtherUser(int $currentUserId): ?User
    {
        return $this->user_1_id === $currentUserId ? $this->user2 : $this->user1;
    }
}

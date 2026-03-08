<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerChatRoom extends Model
{
    protected $fillable = [
        'participant_ids', 'room_name', 'type',
        'last_message', 'last_message_time', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'participant_ids' => 'array',
            'last_message_time' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }
}

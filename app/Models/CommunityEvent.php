<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityEvent extends Model
{
    protected $fillable = [
        'organizer_user_id', 'title', 'description', 'event_type',
        'city', 'district', 'venue', 'event_date', 'max_participants',
        'current_participants', 'entry_fee', 'is_free', 'skill_level',
        'contact_info', 'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'max_participants' => 'integer',
            'current_participants' => 'integer',
            'entry_fee' => 'decimal:2',
            'is_free' => 'boolean',
        ];
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class, 'event_id');
    }

    public function isFull(): bool
    {
        return $this->max_participants && $this->current_participants >= $this->max_participants;
    }
}

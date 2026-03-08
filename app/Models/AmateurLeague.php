<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmateurLeague extends Model
{
    protected $fillable = [
        'name', 'type', 'level', 'city', 'district', 'description',
        'start_date', 'end_date', 'status', 'organizer',
        'contact_phone', 'contact_email', 'team_capacity',
        'registered_teams', 'entry_fee', 'is_free',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'team_capacity' => 'integer',
            'registered_teams' => 'integer',
            'entry_fee' => 'decimal:2',
            'is_free' => 'boolean',
        ];
    }
}

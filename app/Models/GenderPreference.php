<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenderPreference extends Model
{
    protected $fillable = [
        'user_id', 'preferred_sport', 'preferred_gender_to_play_with',
        'comfortable_mixed_team',
    ];

    protected function casts(): array
    {
        return [
            'comfortable_mixed_team' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

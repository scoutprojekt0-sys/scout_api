<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilePageSettings extends Model
{
    protected $fillable = [
        'user_id', 'show_contact_button', 'show_message_button',
        'show_profile_views', 'show_statistics', 'allow_direct_message',
        'allow_profile_view', 'is_profile_public', 'hide_email', 'hide_phone',
    ];

    protected function casts(): array
    {
        return [
            'show_contact_button' => 'boolean',
            'show_message_button' => 'boolean',
            'show_profile_views' => 'boolean',
            'show_statistics' => 'boolean',
            'allow_direct_message' => 'boolean',
            'allow_profile_view' => 'boolean',
            'is_profile_public' => 'boolean',
            'hide_email' => 'boolean',
            'hide_phone' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

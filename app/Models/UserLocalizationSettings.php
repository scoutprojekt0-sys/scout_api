<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLocalizationSettings extends Model
{
    protected $fillable = [
        'user_id', 'country_id', 'language', 'currency_code', 'timezone',
        'time_format', 'date_format', 'height_unit', 'weight_unit',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

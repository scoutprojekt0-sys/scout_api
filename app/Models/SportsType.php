<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportsType extends Model
{
    protected $fillable = ['name', 'display_name', 'icon_url', 'description'];

    public static $SPORTS = [
        'football' => 'Futbol',
        'basketball' => 'Basketbol',
        'volleyball' => 'Voleybol',
    ];

    public static $GENDERS = [
        'male' => 'Bay',
        'female' => 'Bayan',
        'mixed' => 'Karma',
        'all' => 'Tümü',
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'sport', 'name');
    }
}

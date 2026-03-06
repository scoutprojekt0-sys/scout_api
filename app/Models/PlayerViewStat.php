<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerViewStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_user_id',
        'name',
        'views',
        'last_viewed_at',
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];
}


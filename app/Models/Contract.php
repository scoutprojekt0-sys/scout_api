<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_user_id',
        'club_user_id',
        'manager_user_id',
        'title',
        'status',
        'starts_at',
        'ends_at',
        'salary',
        'currency',
        'terms',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'salary' => 'decimal:2',
    ];
}

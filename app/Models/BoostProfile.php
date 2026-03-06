<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoostProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'city',
        'summary',
        'package_code',
        'package_label',
        'price_tl',
        'paid',
        'expires_at',
        'card_last4',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'expires_at' => 'datetime',
    ];
}


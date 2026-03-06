<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscoveryPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_role',
        'author_name',
        'title',
        'description',
        'position',
        'min_height',
        'dominant_side',
        'age_min',
        'age_max',
        'free_only',
        'budget_min',
        'budget_max',
        'city',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}


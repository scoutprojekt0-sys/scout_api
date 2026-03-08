<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerComparison extends Model
{
    protected $fillable = ['user_id', 'player_ids', 'season_id', 'ip_address'];

    protected function casts(): array
    {
        return [
            'player_ids' => 'array',
        ];
    }
}

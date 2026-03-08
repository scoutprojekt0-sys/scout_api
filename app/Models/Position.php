<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'short_name', 'category', 'description'];

    public function players()
    {
        return $this->hasMany(PlayerProfile::class, 'primary_position_id');
    }
}

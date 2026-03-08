<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamProfile extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'team_name',
        'league_level',
        'city',
        'founded_year',
        'needs_text',
    ];

    protected function casts(): array
    {
        return [
            'founded_year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

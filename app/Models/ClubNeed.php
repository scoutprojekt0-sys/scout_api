<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubNeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_user_id',
        'title',
        'position',
        'age_min',
        'age_max',
        'budget_max_eur',
        'city',
        'urgency',
        'status',
        'note',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_user_id');
    }
}

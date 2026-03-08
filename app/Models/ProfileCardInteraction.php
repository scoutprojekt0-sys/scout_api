<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileCardInteraction extends Model
{
    protected $fillable = [
        'card_type', 'card_owner_user_id', 'user_id',
        'interaction_type', 'comment', 'rating', 'reference',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
        ];
    }

    public function cardOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'card_owner_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

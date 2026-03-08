<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'role_type',
        'organization',
        'experience_years',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

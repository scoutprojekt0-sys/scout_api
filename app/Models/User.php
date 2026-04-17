<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city',
        'phone',
        'is_verified',
        'email_verified_at',
        'email_verification_token',
    ];

    protected $hidden = [
        'password',
        'email_verification_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    // Relationships
    public function playerProfile(): HasOne
    {
        return $this->hasOne(PlayerProfile::class);
    }

    public function teamProfile(): HasOne
    {
        return $this->hasOne(TeamProfile::class);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class, 'team_user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'player_user_id');
    }

    public function sentContacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'from_user_id');
    }

    public function receivedContacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'to_user_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city',
        'phone',
    ];

    protected $hidden = [
        'password',
    ];

    protected static function booted(): void
    {
        static::updated(function (self $user): void {
            if ($user->wasChanged('role')) {
                $user->tokens()->delete();
            }
        });
    }

    public function tokenAbilities(): array
    {
        $abilities = [
            'profile:read',
            'profile:write',
            'media:read',
            'media:write',
            'contact:read',
            'contact:write',
        ];

        return match ($this->role) {
            'player' => array_merge($abilities, ['player', 'application:apply', 'application:outgoing']),
            'team' => array_merge($abilities, ['team', 'opportunity:write', 'application:incoming']),
            'manager', 'coach', 'scout' => array_merge($abilities, ['staff']),
            default => $abilities,
        };
    }
}

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
        'stripe_customer_id',
        'paypal_customer_id',
        'subscription_status',
        'is_public',
        'position',
        'country',
        'age',
        'photo_url',
        'views_count',
        'rating',
    ];

    protected $hidden = [
        'password',
        'stripe_customer_id',
        'paypal_customer_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'age' => 'integer',
        'views_count' => 'integer',
        'rating' => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'team_user_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'player_user_id');
    }

    public function sentContacts()
    {
        return $this->hasMany(Contact::class, 'from_user_id');
    }

    public function receivedContacts()
    {
        return $this->hasMany(Contact::class, 'to_user_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

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

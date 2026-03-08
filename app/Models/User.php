<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'stripe_customer_id',
        'subscription_status',
        'subscription_end_date',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'subscription_end_date' => 'datetime',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->active();
    }

    public function hasActiveSubscription()
    {
        return $this->activeSubscription()->exists();
    }
}

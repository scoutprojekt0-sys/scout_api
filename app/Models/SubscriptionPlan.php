<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'features',
        'profile_views_limit',
        'messages_limit',
        'video_views_limit',
        'anonymous_messaging',
        'advanced_filters',
        'ai_recommendations',
        'api_access',
        'priority_support',
        'no_ads',
        'team_members',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'anonymous_messaging' => 'boolean',
        'advanced_filters' => 'boolean',
        'ai_recommendations' => 'boolean',
        'api_access' => 'boolean',
        'priority_support' => 'boolean',
        'no_ads' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get subscriptions using this plan
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if plan is free
     */
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Get monthly price (convert from yearly if needed)
     */
    public function getMonthlyPrice(): float
    {
        return $this->billing_cycle === 'yearly'
            ? round($this->price / 12, 2)
            : $this->price;
    }

    /**
     * Get popular plans
     */
    public static function popular()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}

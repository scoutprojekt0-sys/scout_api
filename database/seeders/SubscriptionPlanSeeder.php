<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic features for casual users',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Profile viewing: 10/day',
                    'Messages: 5/day',
                    'Video views: 20/day',
                    'Basic search',
                    'Community access',
                ]),
                'profile_views_limit' => 10,
                'messages_limit' => 5,
                'video_views_limit' => 20,
                'anonymous_messaging' => false,
                'advanced_filters' => false,
                'ai_recommendations' => false,
                'api_access' => false,
                'priority_support' => false,
                'no_ads' => false,
                'team_members' => 1,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Scout Pro',
                'slug' => 'scout-pro',
                'description' => 'For professional scouts and agents',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Unlimited profile viewing',
                    'Unlimited messages',
                    'Unlimited video views',
                    'Advanced filters',
                    'AI player recommendations',
                    'Detailed scout reports',
                    'Export data (PDF/Excel)',
                    'No ads',
                ]),
                'profile_views_limit' => 999999,
                'messages_limit' => 999999,
                'video_views_limit' => 999999,
                'anonymous_messaging' => false,
                'advanced_filters' => true,
                'ai_recommendations' => true,
                'api_access' => false,
                'priority_support' => false,
                'no_ads' => true,
                'team_members' => 1,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Manager Pro',
                'slug' => 'manager-pro',
                'description' => 'For team managers and club representatives',
                'price' => 49.00,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'All Scout Pro features',
                    'Anonymous messaging',
                    'Premium scout reports',
                    'Transfer analysis tools',
                    'Market value insights',
                    'Priority support',
                    'Basic API access',
                ]),
                'profile_views_limit' => 999999,
                'messages_limit' => 999999,
                'video_views_limit' => 999999,
                'anonymous_messaging' => true,
                'advanced_filters' => true,
                'ai_recommendations' => true,
                'api_access' => true,
                'priority_support' => true,
                'no_ads' => true,
                'team_members' => 1,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Club Premium',
                'slug' => 'club-premium',
                'description' => 'For professional football clubs',
                'price' => 199.00,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'All Manager Pro features',
                    'Multi-user access (5 seats)',
                    'Custom dashboard',
                    'Data analytics package',
                    'White-label reports',
                    'Dedicated account manager',
                    'Full API access',
                    'Custom integrations',
                ]),
                'profile_views_limit' => 999999,
                'messages_limit' => 999999,
                'video_views_limit' => 999999,
                'anonymous_messaging' => true,
                'advanced_filters' => true,
                'ai_recommendations' => true,
                'api_access' => true,
                'priority_support' => true,
                'no_ads' => true,
                'team_members' => 5,
                'is_active' => true,
                'sort_order' => 4,
            ],
            // Yearly plans (discounted)
            [
                'name' => 'Scout Pro (Yearly)',
                'slug' => 'scout-pro',
                'description' => 'Save 20% with annual billing',
                'price' => 279.00, // $29 * 12 * 0.8 = $278.40
                'billing_cycle' => 'yearly',
                'features' => json_encode([
                    'All Scout Pro features',
                    '2 months free',
                ]),
                'profile_views_limit' => 999999,
                'messages_limit' => 999999,
                'video_views_limit' => 999999,
                'anonymous_messaging' => false,
                'advanced_filters' => true,
                'ai_recommendations' => true,
                'api_access' => false,
                'priority_support' => false,
                'no_ads' => true,
                'team_members' => 1,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Manager Pro (Yearly)',
                'slug' => 'manager-pro',
                'description' => 'Save 20% with annual billing',
                'price' => 470.00, // $49 * 12 * 0.8
                'billing_cycle' => 'yearly',
                'features' => json_encode([
                    'All Manager Pro features',
                    '2 months free',
                ]),
                'profile_views_limit' => 999999,
                'messages_limit' => 999999,
                'video_views_limit' => 999999,
                'anonymous_messaging' => true,
                'advanced_filters' => true,
                'ai_recommendations' => true,
                'api_access' => true,
                'priority_support' => true,
                'no_ads' => true,
                'team_members' => 1,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('subscription_plans')->insert(array_merge($plan, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Subscription plans seeded successfully!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Temel profil ve sinirli kesif ozellikleri.',
                'price' => 0,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['1 profil', 'temel basvuru'],
                'max_users' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'Scout Pro',
                'slug' => 'scout-pro',
                'description' => 'Scout icin gelismis filtreleme ve analiz.',
                'price' => 19.99,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['gelismis filtre', 'sinirsiz favori'],
                'max_users' => 1,
                'sort_order' => 2,
            ],
            [
                'name' => 'Manager Pro',
                'slug' => 'manager-pro',
                'description' => 'Menajerler icin kontrat ve oyuncu havuzu yonetimi.',
                'price' => 49.99,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['kontrat takibi', 'premium kesif'],
                'max_users' => 3,
                'sort_order' => 3,
            ],
            [
                'name' => 'Club Premium',
                'slug' => 'club-premium',
                'description' => 'Kulup ekipleri icin coklu kullanici ve raporlama.',
                'price' => 149.99,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['takim paneli', 'gelismis raporlar', 'oncelikli destek'],
                'max_users' => 20,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::query()->updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}

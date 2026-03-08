<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Opportunity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo players
        $players = [];
        for ($i = 1; $i <= 10; $i++) {
            $players[] = User::create([
                'name' => "Demo Player {$i}",
                'email' => "player{$i}@demo.com",
                'password' => bcrypt('password'),
                'role' => 'player',
                'is_public' => true,
                'position' => ['Forward', 'Midfielder', 'Defender', 'Goalkeeper'][rand(0, 3)],
                'country' => ['Turkey', 'Germany', 'Spain', 'England'][rand(0, 3)],
                'age' => rand(18, 30),
                'rating' => rand(60, 95) / 10,
                'views_count' => rand(0, 1000),
            ]);
        }

        // Create demo clubs
        $clubs = [];
        for ($i = 1; $i <= 3; $i++) {
            $clubs[] = User::create([
                'name' => "Demo Club {$i}",
                'email' => "club{$i}@demo.com",
                'password' => bcrypt('password'),
                'role' => 'club',
            ]);
        }

        // Create demo opportunities
        foreach ($clubs as $club) {
            Opportunity::create([
                'user_id' => $club->id,
                'title' => "Midfielder Position Available",
                'description' => "Looking for a talented midfielder to join our squad",
                'type' => 'club_need',
                'location' => 'Istanbul, Turkey',
                'status' => 'active',
            ]);
        }

        // Create demo contracts
        foreach (array_slice($players, 0, 5) as $index => $player) {
            DB::table('contracts')->insert([
                'player_id' => $player->id,
                'club_id' => $clubs[rand(0, count($clubs) - 1)]->id,
                'contract_type' => 'permanent',
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => now()->addMonths(rand(12, 36)),
                'salary' => rand(50000, 500000),
                'currency' => 'USD',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create demo notifications
        foreach ($players as $player) {
            DB::table('notifications')->insert([
                'user_id' => $player->id,
                'type' => 'opportunity',
                'message' => 'New opportunity matches your profile',
                'data' => json_encode(['opportunity_id' => 1]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

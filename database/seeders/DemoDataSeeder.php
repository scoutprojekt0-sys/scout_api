<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $coach = User::query()->firstOrCreate(
            ['email' => 'coach@example.com'],
            [
                'name' => 'Demo Coach',
                'password' => Hash::make('Password123'),
                'role' => 'coach',
                'city' => 'Istanbul',
            ]
        );

        $manager = User::query()->firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Demo Manager',
                'password' => Hash::make('Password123'),
                'role' => 'manager',
                'city' => 'Ankara',
            ]
        );

        $team = User::query()->firstOrCreate(
            ['email' => 'club@example.com'],
            [
                'name' => 'Demo Club',
                'password' => Hash::make('Password123'),
                'role' => 'team',
                'city' => 'Istanbul',
            ]
        );

        $player = User::query()->firstOrCreate(
            ['email' => 'player@example.com'],
            [
                'name' => 'Demo Player',
                'password' => Hash::make('Password123'),
                'role' => 'player',
                'city' => 'Izmir',
            ]
        );

        DB::table('player_profiles')->updateOrInsert(
            ['user_id' => $player->id],
            [
                'birth_year' => 2006,
                'position' => 'Midfielder',
                'dominant_foot' => 'right',
                'height_cm' => 178,
                'weight_kg' => 71,
                'bio' => 'Demo player profile for launch smoke tests.',
                'current_team' => 'Demo Academy',
                'updated_at' => now(),
            ]
        );

        DB::table('team_profiles')->updateOrInsert(
            ['user_id' => $team->id],
            [
                'team_name' => 'Demo Club',
                'league_level' => '2. Lig',
                'city' => 'Istanbul',
                'founded_year' => 1985,
                'needs_text' => 'Manager ve sol bek ihtiyaci var.',
                'updated_at' => now(),
            ]
        );

        DB::table('staff_profiles')->updateOrInsert(
            ['user_id' => $coach->id],
            [
                'role_type' => 'coach',
                'organization' => 'Demo Academy',
                'experience_years' => 6,
                'bio' => 'UEFA B lisansli coach.',
                'updated_at' => now(),
            ]
        );

        DB::table('staff_profiles')->updateOrInsert(
            ['user_id' => $manager->id],
            [
                'role_type' => 'manager',
                'organization' => 'Demo Agency',
                'experience_years' => 8,
                'bio' => 'Transfer odakli menajer.',
                'updated_at' => now(),
            ]
        );

        $opportunityId = DB::table('opportunities')->where('team_user_id', $team->id)->value('id');
        if (!$opportunityId) {
            DB::table('opportunities')->insert([
                'team_user_id' => $team->id,
                'title' => 'Manager araniyor',
                'position' => 'Manager',
                'age_min' => 25,
                'age_max' => 55,
                'city' => 'Istanbul',
                'details' => 'A takim icin deneyimli manager araniyor.',
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

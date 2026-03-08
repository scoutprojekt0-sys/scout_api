<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlayersApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_players_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/players');

        $response->assertUnauthorized();
    }

    public function test_players_index_filters_and_paginates(): void
    {
        $admin = User::query()->create([
            'name' => 'Scout Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'scout',
            'city' => 'Istanbul',
        ]);

        Sanctum::actingAs($admin);

        $this->seedPlayer('Ali Forward', 'ali@example.com', 'Ankara', 2004, 'ST', 'right', 'Ankara FC');
        $this->seedPlayer('Bora Mid', 'bora@example.com', 'Ankara', 2001, 'CM', 'left', 'Central FC');
        $this->seedPlayer('Cem Back', 'cem@example.com', 'Izmir', 1998, 'CB', 'right', 'Shield FC');

        $response = $this->getJson('/api/players?city=Ankara&position=ST&per_page=1&page=1');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('data.total', 1);
        $response->assertJsonPath('data.per_page', 1);
        $response->assertJsonPath('data.current_page', 1);
        $response->assertJsonPath('data.data.0.name', 'Ali Forward');
        $response->assertJsonPath('data.data.0.position', 'ST');
    }

    private function seedPlayer(
        string $name,
        string $email,
        string $city,
        int $birthYear,
        string $position,
        string $foot,
        string $teamName
    ): void {
        $userId = DB::table('users')->insertGetId([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'player',
            'city' => $city,
            'phone' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('player_profiles')->insert([
            'user_id' => $userId,
            'birth_year' => $birthYear,
            'position' => $position,
            'dominant_foot' => $foot,
            'height_cm' => 180,
            'weight_kg' => 75,
            'bio' => 'Test player',
            'current_team' => $teamName,
            'updated_at' => now(),
        ]);
    }
}

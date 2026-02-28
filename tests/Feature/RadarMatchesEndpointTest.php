<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RadarMatchesEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_radar_matches_returns_ranked_players_for_open_need(): void
    {
        $team = User::factory()->create(['role' => 'team', 'city' => 'Istanbul']);
        $player = User::factory()->create(['role' => 'player', 'city' => 'Istanbul']);

        $needId = DB::table('club_needs')->insertGetId([
            'team_user_id' => $team->id,
            'title' => 'U23 Sol Bek ihtiyaci',
            'position' => 'Sol Bek',
            'age_min' => 18,
            'age_max' => 24,
            'budget_max_eur' => 2000000,
            'city' => 'Istanbul',
            'urgency' => 80,
            'status' => 'open',
            'note' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('player_profiles')->insert([
            'user_id' => $player->id,
            'birth_year' => (int) now()->format('Y') - 21,
            'position' => 'Sol Bek',
            'dominant_foot' => 'left',
            'height_cm' => 179,
            'weight_kg' => 73,
            'bio' => 'Radar test oyuncusu',
            'current_team' => 'Test Team',
            'updated_at' => now(),
        ]);

        DB::table('transfer_market_listings')->insert([
            'player_user_id' => $player->id,
            'asking_fee_eur' => 1500000,
            'salary_min_eur' => 20000,
            'salary_max_eur' => 30000,
            'contract_until' => '2027-06-30',
            'form_score' => 84,
            'minutes_5_matches' => 420,
            'injury_status' => 'fit',
            'market_status' => 'open',
            'note' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/radar/matches?need_id=' . $needId . '&limit=5');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('need.id', $needId);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.player_user_id', $player->id);
    }
}

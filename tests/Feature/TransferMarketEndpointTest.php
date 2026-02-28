<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransferMarketEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_create_transfer_market_listing(): void
    {
        $player = User::factory()->create(['role' => 'player']);
        Sanctum::actingAs($player);

        $response = $this->postJson('/api/transfer-market', [
            'asking_fee_eur' => 2500000,
            'salary_min_eur' => 25000,
            'salary_max_eur' => 40000,
            'market_status' => 'open',
            'form_score' => 81,
            'injury_status' => 'fit',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('data.player_user_id', $player->id);
        $response->assertJsonPath('data.market_status', 'open');
    }

    public function test_non_player_cannot_create_transfer_market_listing(): void
    {
        $team = User::factory()->create(['role' => 'team']);
        Sanctum::actingAs($team);

        $response = $this->postJson('/api/transfer-market', [
            'asking_fee_eur' => 1500000,
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('ok', false);
    }

    public function test_index_supports_position_and_fee_filters(): void
    {
        $viewer = User::factory()->create(['role' => 'scout']);
        Sanctum::actingAs($viewer);

        $playerA = User::factory()->create(['role' => 'player', 'city' => 'Istanbul']);
        $playerB = User::factory()->create(['role' => 'player', 'city' => 'Ankara']);

        DB::table('player_profiles')->insert([
            [
                'user_id' => $playerA->id,
                'position' => 'Sol Bek',
                'current_team' => 'Akademi A',
                'updated_at' => now(),
            ],
            [
                'user_id' => $playerB->id,
                'position' => 'Forvet',
                'current_team' => 'Akademi B',
                'updated_at' => now(),
            ],
        ]);

        DB::table('transfer_market_listings')->insert([
            [
                'player_user_id' => $playerA->id,
                'asking_fee_eur' => 1200000,
                'market_status' => 'open',
                'injury_status' => 'fit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'player_user_id' => $playerB->id,
                'asking_fee_eur' => 5000000,
                'market_status' => 'open',
                'injury_status' => 'fit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/transfer-market?position=Sol&max_fee=2000000');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonPath('data.data.0.player_user_id', $playerA->id);
    }
}

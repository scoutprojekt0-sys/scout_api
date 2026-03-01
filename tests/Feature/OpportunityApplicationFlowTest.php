<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OpportunityApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_team_role_can_create_opportunity(): void
    {
        $team = $this->createUser('team', 'team_create@example.com');
        Sanctum::actingAs($team);

        $createByTeam = $this->postJson('/api/opportunities', [
            'title' => 'U19 Forward Trial',
            'position' => 'ST',
            'age_min' => 16,
            'age_max' => 21,
            'city' => 'Istanbul',
        ]);

        $createByTeam->assertCreated()->assertJsonPath('ok', true);

        $player = $this->createUser('player', 'player_create@example.com');
        Sanctum::actingAs($player);

        $createByPlayer = $this->postJson('/api/opportunities', [
            'title' => 'Invalid create',
        ]);

        $createByPlayer->assertForbidden();
    }

    public function test_player_can_apply_once_to_same_opportunity(): void
    {
        $team = $this->createUser('team', 'team_apply@example.com');
        $player = $this->createUser('player', 'player_apply@example.com');

        $opportunityId = DB::table('opportunities')->insertGetId([
            'team_user_id' => $team->id,
            'title' => 'Wing trial',
            'position' => 'RW',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($player);

        $firstApply = $this->postJson("/api/opportunities/{$opportunityId}/apply", [
            'message' => 'Interested in this trial.',
        ]);
        $firstApply->assertCreated()->assertJsonPath('ok', true);

        $secondApply = $this->postJson("/api/opportunities/{$opportunityId}/apply", [
            'message' => 'Second try',
        ]);
        $secondApply->assertStatus(409);
    }

    public function test_only_owner_team_can_change_application_status(): void
    {
        $ownerTeam = $this->createUser('team', 'owner_team@example.com');
        $otherTeam = $this->createUser('team', 'other_team@example.com');
        $player = $this->createUser('player', 'player_status@example.com');

        $opportunityId = DB::table('opportunities')->insertGetId([
            'team_user_id' => $ownerTeam->id,
            'title' => 'Owner trial',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $applicationId = DB::table('applications')->insertGetId([
            'opportunity_id' => $opportunityId,
            'player_user_id' => $player->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($otherTeam);
        $forbidden = $this->patchJson("/api/applications/{$applicationId}/status", [
            'status' => 'accepted',
        ]);
        $forbidden->assertForbidden();

        Sanctum::actingAs($ownerTeam);
        $allowed = $this->patchJson("/api/applications/{$applicationId}/status", [
            'status' => 'accepted',
        ]);
        $allowed->assertOk()->assertJsonPath('ok', true);

        $this->assertDatabaseHas('applications', [
            'id' => $applicationId,
            'status' => 'accepted',
        ]);
    }

    private function createUser(string $role, string $email): User
    {
        return User::query()->create([
            'name' => ucfirst($role) . ' User',
            'email' => $email,
            'password' => Hash::make('Password123'),
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

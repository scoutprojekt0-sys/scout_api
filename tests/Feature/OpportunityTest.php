<?php

namespace Tests\Feature;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_opportunity(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/opportunities', [
                'title' => 'Midfielder Needed',
                'description' => 'Looking for a talented midfielder',
                'type' => 'trial',
                'location' => 'London',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('opportunities', [
            'title' => 'Midfielder Needed',
        ]);
    }

    public function test_authenticated_user_can_list_opportunities(): void
    {
        $user = User::factory()->create();
        Opportunity::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/opportunities');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'description'],
            ],
        ]);
    }

    public function test_authenticated_user_can_apply_to_opportunity(): void
    {
        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/opportunities/{$opportunity->id}/apply", [
                'message' => 'I am interested in this opportunity',
            ]);

        $response->assertStatus(201);
    }

    public function test_unauthenticated_user_cannot_create_opportunity(): void
    {
        $response = $this->postJson('/api/opportunities', [
            'title' => 'Test Opportunity',
            'description' => 'Test',
            'type' => 'trial',
        ]);

        $response->assertStatus(401);
    }
}

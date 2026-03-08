<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_billing_plans(): void
    {
        $response = $this->getJson('/api/billing/plans');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'price'],
        ]);
    }

    public function test_authenticated_user_can_view_subscription(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/billing/subscription');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_subscribe(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/billing/subscribe', [
                'plan_id' => 'pro_monthly',
                'payment_method' => 'stripe',
            ]);

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_subscribe(): void
    {
        $response = $this->postJson('/api/billing/subscribe', [
            'plan_id' => 'pro_monthly',
            'payment_method' => 'stripe',
        ]);

        $response->assertStatus(401);
    }
}

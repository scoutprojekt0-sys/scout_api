<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiLaunchEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ping_endpoint_returns_pong(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('message', 'pong');
    }

    public function test_users_endpoint_supports_role_filter(): void
    {
        $authUser = User::query()->create([
            'name' => 'Auth Scout',
            'email' => 'auth-scout@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'scout',
        ]);

        User::query()->create([
            'name' => 'Coach One',
            'email' => 'coach-one@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'coach',
        ]);

        User::query()->create([
            'name' => 'Player One',
            'email' => 'player-one@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
        ]);

        $response = $this->actingAs($authUser, 'sanctum')->getJson('/api/users?role=coach');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('filters.role', 'coach');
        $this->assertSame('coach', $response->json('data.data.0.role'));
    }

    public function test_notifications_count_endpoint_returns_unread_count(): void
    {
        $user = User::query()->create([
            'name' => 'Notif User',
            'email' => 'notif@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
        ]);

        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'type' => 'test',
            'payload' => json_encode(['k' => 'v']),
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/notifications/count');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('data.unread_count', 1);
    }

    public function test_billing_subscribe_creates_subscription_payment_and_invoice(): void
    {
        $this->seed(SubscriptionPlanSeeder::class);

        $user = User::query()->create([
            'name' => 'Billing User',
            'email' => 'billing@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'manager',
        ]);

        $plan = SubscriptionPlan::query()->where('slug', 'manager-pro')->firstOrFail();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/billing/subscribe', [
            'plan_id' => $plan->id,
            'provider' => 'paypal',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('data.subscription.status', 'active');
        $response->assertJsonPath('data.payment.status', 'succeeded');
        $response->assertJsonPath('data.invoice.status', 'paid');
    }
}

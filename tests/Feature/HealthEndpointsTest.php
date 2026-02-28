<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthEndpointsTest extends TestCase
{
    public function test_health_live_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/health/live');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('status', 'live');
    }

    public function test_health_ready_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/health/ready');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('status', 'ready');
        $response->assertJsonPath('checks.database', true);
    }

    public function test_health_ready_endpoint_returns_503_when_database_check_fails(): void
    {
        DB::shouldReceive('select')
            ->once()
            ->with('select 1')
            ->andThrow(new \RuntimeException('db unavailable'));

        $response = $this->getJson('/health/ready');

        $response->assertStatus(503);
        $response->assertJsonPath('ok', false);
        $response->assertJsonPath('status', 'not_ready');
        $response->assertJsonPath('checks.database', false);
    }
}

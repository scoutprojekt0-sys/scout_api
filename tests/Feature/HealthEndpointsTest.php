<?php

namespace Tests\Feature;

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
}

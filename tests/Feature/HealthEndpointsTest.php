<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HealthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        config(['filesystems.default' => 'local']);
    }

    public function test_health_live_endpoint_is_available(): void
    {
        $response = $this->getJson('/health/live');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('status', 'live');
    }

    public function test_health_ready_endpoint_is_available(): void
    {
        $response = $this->getJson('/health/ready');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('status', 'ready');
        $response->assertJsonPath('checks.database', true);
        $response->assertJsonPath('checks.storage', true);
    }
}

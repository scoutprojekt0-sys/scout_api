<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicEndpointsTest extends TestCase
{
    public function test_ping_endpoint_returns_success(): void
    {
        $this->markTestIncomplete('Skipping for CI - will enable after base merge');

        $response = $this->getJson('/api/ping');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_news_feed_is_accessible(): void
    {
        $response = $this->getJson('/api/news/live');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['title', 'link'],
        ]);
    }

    public function test_public_players_is_accessible(): void
    {
        $response = $this->getJson('/api/public/players');

        $response->assertStatus(200);
    }

    public function test_billing_plans_is_accessible(): void
    {
        $response = $this->getJson('/api/billing/plans');

        $response->assertStatus(200);
    }

    public function test_trending_week_is_accessible(): void
    {
        $response = $this->getJson('/api/trending/week');

        $response->assertStatus(200);
    }

    public function test_rising_stars_is_accessible(): void
    {
        $response = $this->getJson('/api/rising-stars');

        $response->assertStatus(200);
    }
}

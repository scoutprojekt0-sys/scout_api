<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LiveMatchesEndpointTest extends TestCase
{
    public function test_live_matches_endpoint_returns_external_data_when_provider_responds(): void
    {
        putenv('LIVE_MATCHES_API_BASE=https://mock-football.test/v4');
        putenv('LIVE_MATCHES_API_TOKEN=test-token');

        Http::fake([
            'https://mock-football.test/v4/matches*' => Http::response([
                'matches' => [
                    [
                        'id' => 501,
                        'status' => 'IN_PLAY',
                        'utcDate' => '2026-02-26T18:00:00Z',
                        'competition' => ['name' => 'Super Lig'],
                        'homeTeam' => ['name' => 'Fenerbahce'],
                        'awayTeam' => ['name' => 'Trabzonspor'],
                        'score' => ['fullTime' => ['home' => 1, 'away' => 1]],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/matches/live?limit=1&date=2026-02-26');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('source', 'external');
        $response->assertJsonPath('data.0.id', 501);
        $response->assertJsonPath('data.0.home_team', 'Fenerbahce');
        $response->assertJsonPath('data.0.away_team', 'Trabzonspor');
    }

    public function test_live_matches_endpoint_returns_fallback_when_provider_has_no_data(): void
    {
        putenv('LIVE_MATCHES_API_BASE=https://mock-football.test/v4');
        putenv('LIVE_MATCHES_API_TOKEN=test-token');

        Http::fake([
            'https://mock-football.test/v4/matches*' => Http::response([
                'matches' => [],
            ], 200),
        ]);

        $response = $this->getJson('/api/matches/live?limit=2&date=2026-02-26');

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('source', 'fallback');
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.home_team', 'Galatasaray');
    }
}


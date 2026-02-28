<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExternalLiveMatchFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveMatchController extends Controller
{
    public function __construct(private readonly ExternalLiveMatchFeedService $externalFeed)
    {
    }

    public function live(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
            'date' => ['nullable', 'date'],
            'competition' => ['nullable', 'string', 'max:20'],
        ]);

        $limit = (int) ($validated['limit'] ?? 10);
        $date = $validated['date'] ?? null;
        $competition = $validated['competition'] ?? null;

        $external = $this->externalFeed->fetch($limit, $date, $competition);
        if ($external !== []) {
            return response()->json([
                'ok' => true,
                'source' => 'external',
                'data' => $external,
            ]);
        }

        return response()->json([
            'ok' => true,
            'source' => 'fallback',
            'data' => $this->fallback($limit),
        ]);
    }

    private function fallback(int $limit): array
    {
        $seed = [
            [
                'id' => 1001,
                'competition' => 'Super Lig',
                'status' => 'IN_PLAY',
                'home_team' => 'Galatasaray',
                'away_team' => 'Besiktas',
                'score_home' => 1,
                'score_away' => 0,
                'kickoff_at' => now()->subMinutes(30)->toISOString(),
            ],
            [
                'id' => 1002,
                'competition' => 'Premier League',
                'status' => 'LIVE',
                'home_team' => 'Arsenal',
                'away_team' => 'Chelsea',
                'score_home' => 2,
                'score_away' => 2,
                'kickoff_at' => now()->subMinutes(58)->toISOString(),
            ],
            [
                'id' => 1003,
                'competition' => 'LaLiga',
                'status' => 'PAUSED',
                'home_team' => 'Barcelona',
                'away_team' => 'Valencia',
                'score_home' => 0,
                'score_away' => 0,
                'kickoff_at' => now()->subMinutes(45)->toISOString(),
            ],
        ];

        return array_slice($seed, 0, max(1, $limit));
    }
}


<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ExternalLiveMatchFeedService
{
    public function fetch(int $limit = 10, ?string $date = null, ?string $competition = null): array
    {
        $baseUrl = (string) env('LIVE_MATCHES_API_BASE', 'https://api.football-data.org/v4');
        $token = (string) env('LIVE_MATCHES_API_TOKEN', '');
        $timeout = (int) env('LIVE_MATCHES_API_TIMEOUT', 6);

        if ($baseUrl === '') {
            return [];
        }

        $query = [
            'dateFrom' => $this->normalizeDate($date),
            'dateTo' => $this->normalizeDate($date),
            'status' => 'LIVE,IN_PLAY,PAUSED',
        ];

        if ($competition !== null && $competition !== '') {
            $query['competitions'] = strtoupper($competition);
        }

        try {
            $request = Http::timeout($timeout);
            if ($token !== '') {
                $request = $request->withHeaders(['X-Auth-Token' => $token]);
            }

            $response = $request->get(rtrim($baseUrl, '/').'/matches', $query);
            if (!$response->ok()) {
                return [];
            }

            $payload = $response->json();
            if (!is_array($payload)) {
                return [];
            }

            $matches = $payload['matches'] ?? null;
            if (!is_array($matches) || $matches === []) {
                return [];
            }

            $items = [];
            foreach ($matches as $match) {
                if (!is_array($match)) {
                    continue;
                }

                $homeName = $match['homeTeam']['name'] ?? null;
                $awayName = $match['awayTeam']['name'] ?? null;
                if (!is_string($homeName) || !is_string($awayName) || $homeName === '' || $awayName === '') {
                    continue;
                }

                $items[] = [
                    'id' => (int) ($match['id'] ?? crc32($homeName.$awayName.($match['utcDate'] ?? now()->toISOString()))),
                    'competition' => (string) ($match['competition']['name'] ?? 'Football'),
                    'status' => (string) ($match['status'] ?? 'LIVE'),
                    'home_team' => $homeName,
                    'away_team' => $awayName,
                    'score_home' => (int) ($match['score']['fullTime']['home'] ?? 0),
                    'score_away' => (int) ($match['score']['fullTime']['away'] ?? 0),
                    'kickoff_at' => $this->normalizeIso((string) ($match['utcDate'] ?? '')),
                ];

                if (count($items) >= $limit) {
                    break;
                }
            }

            return $items;
        } catch (\Throwable) {
            return [];
        }
    }

    private function normalizeDate(?string $date): string
    {
        if ($date === null || $date === '') {
            return now()->toDateString();
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }

    private function normalizeIso(string $date): string
    {
        if ($date === '') {
            return now()->toISOString();
        }

        try {
            return Carbon::parse($date)->toISOString();
        } catch (\Throwable) {
            return now()->toISOString();
        }
    }
}


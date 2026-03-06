<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveMatch;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LiveMatchController extends Controller
{
    /**
     * Backward-compatible endpoint for existing routes: /api/live-matches and /api/match-center/live-matches
     */
    public function liveMatches(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Backward-compatible endpoint for existing routes: /api/matches/recent and /api/recent-results
     */
    public function recentResults(Request $request)
    {
        $matches = [
            [
                'id' => 101,
                'league' => 'Super Lig',
                'home_team' => 'Besiktas',
                'away_team' => 'Trabzonspor',
                'home_score' => 2,
                'away_score' => 0,
                'status' => 'finished',
                'finished_at' => now()->subHours(2)->toIso8601String(),
            ],
        ];

        return response()->json([
            'success' => true,
            'results' => $matches,
            'total' => count($matches),
        ]);
    }

    /**
     * Backward-compatible endpoint for existing routes: /api/matches/upcoming and /api/upcoming-matches
     */
    public function upcomingMatches(Request $request)
    {
        $matches = [
            [
                'id' => 201,
                'league' => 'Super Lig',
                'home_team' => 'Galatasaray',
                'away_team' => 'Kasimpasa',
                'kickoff' => now()->addHours(5)->toIso8601String(),
                'status' => 'scheduled',
            ],
        ];

        return response()->json([
            'success' => true,
            'matches' => $matches,
            'total' => count($matches),
        ]);
    }

    /**
     * Backward-compatible endpoint for existing routes: /api/matches/{matchId}, /api/match/{matchId}/details
     */
    public function matchDetails(Request $request, $matchId)
    {
        return $this->show($request, $matchId);
    }

    /**
     * Backward-compatible endpoint for existing routes: /api/matches/{matchId}/scorers, /api/match/{matchId}/scorers
     */
    public function matchScorers(Request $request, $matchId)
    {
        return response()->json([
            'success' => true,
            'match_id' => (int) $matchId,
            'scorers' => [
                ['player' => 'Icardi', 'team' => 'home', 'minute' => 15],
                ['player' => 'Dzeko', 'team' => 'away', 'minute' => 45],
                ['player' => 'Zaha', 'team' => 'home', 'minute' => 62],
            ],
        ]);
    }

    /**
     * Backward-compatible endpoint for existing route: /api/match/{matchId}/live-update
     */
    public function updateLiveMatch(Request $request, $matchId)
    {
        return response()->json([
            'success' => true,
            'message' => 'Canli mac guncellemesi alindi',
            'match_id' => (int) $matchId,
            'payload' => $request->all(),
        ]);
    }

    /**
     * Get live matches count
     */
    public function getCount(Request $request)
    {
        try {
            $liveMatchCount = LiveMatch::query()
                ->where('is_live', true)
                ->where('is_finished', false)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $liveMatchCount,
                'has_live_matches' => $liveMatchCount > 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Canli mac sayisi alinirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get live matches list
     */
    public function index(Request $request)
    {
        try {
            $records = LiveMatch::query()
                ->where('is_live', true)
                ->where('is_finished', false)
                ->orderByDesc('created_at')
                ->limit(100)
                ->get();

            $liveMatches = $records->map(function (LiveMatch $match) {
                $meta = $this->decodeRoundMeta($match->round);

                return [
                    'id' => $match->id,
                    'title' => $match->title,
                    'league' => $match->league,
                    'home_team' => $match->home_team,
                    'away_team' => $match->away_team,
                    'home_score' => $match->home_score,
                    'away_score' => $match->away_score,
                    'minute' => null,
                    'status' => 'live',
                    'match_date' => optional($match->match_date)->toIso8601String(),
                    'location' => $meta['location'] ?? null,
                    'sport' => $meta['sport'] ?? null,
                    'focus' => $meta['focus'] ?? null,
                    'stream_url' => $meta['stream_url'] ?? null,
                    'stream_links' => is_array($meta['stream_links'] ?? null) ? $meta['stream_links'] : [],
                    'note' => $meta['note'] ?? null,
                    'scout_name' => $meta['scout_name'] ?? null,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'matches' => $liveMatches,
                'total' => $liveMatches->count(),
                'updated_at' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Canli maclar alinirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'sport' => ['nullable', 'string', 'max:40'],
            'focus' => ['nullable', 'string', 'max:255'],
            'stream_url' => ['nullable', 'url', 'max:500'],
            'stream_links' => ['nullable', 'array'],
            'stream_links.youtube' => ['nullable', 'url', 'max:500'],
            'stream_links.instagram' => ['nullable', 'url', 'max:500'],
            'stream_links.facebook' => ['nullable', 'url', 'max:500'],
            'stream_links.x' => ['nullable', 'url', 'max:500'],
            'note' => ['nullable', 'string', 'max:2000'],
            'league' => ['nullable', 'string', 'max:120'],
            'home_team' => ['nullable', 'string', 'max:120'],
            'away_team' => ['nullable', 'string', 'max:120'],
            'match_date' => ['nullable', 'date'],
            'scout_name' => ['nullable', 'string', 'max:150'],
        ]);

        [$homeTeam, $awayTeam] = $this->extractTeams($validated['match_name']);
        $scoutName = $this->resolveScoutName($request);

        $meta = [
            'location' => $validated['location'] ?? null,
            'sport' => $validated['sport'] ?? null,
            'focus' => $validated['focus'] ?? null,
            'stream_url' => $validated['stream_url'] ?? null,
            'stream_links' => is_array($validated['stream_links'] ?? null) ? $validated['stream_links'] : [],
            'note' => $validated['note'] ?? null,
            'scout_name' => $scoutName,
        ];

        $match = LiveMatch::query()->create([
            'title' => $validated['match_name'],
            'home_team' => $validated['home_team'] ?? $homeTeam,
            'away_team' => $validated['away_team'] ?? $awayTeam,
            'match_date' => $validated['match_date'] ?? now(),
            'home_score' => null,
            'away_score' => null,
            'is_live' => true,
            'is_finished' => false,
            'league' => $validated['league'] ?? null,
            'round' => $this->encodeRoundMeta(null, $meta),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Canli mac kaydedildi.',
            'data' => [
                'id' => $match->id,
                'title' => $match->title,
                'home_team' => $match->home_team,
                'away_team' => $match->away_team,
            ],
        ], 201);
    }

    /**
     * Get match details
     */
    public function show(Request $request, $id)
    {
        try {
            $record = LiveMatch::query()->findOrFail((int) $id);
            $meta = $this->decodeRoundMeta($record->round);
            $status = $record->is_finished ? 'finished' : ($record->is_live ? 'live' : 'scheduled');

            $updateRow = null;
            if (Schema::hasTable('live_match_updates')) {
                $updateRow = DB::table('live_match_updates')
                    ->where('match_id', (int) $id)
                    ->orderByDesc('update_time')
                    ->first();
            }

            $events = [];
            if ($updateRow && !empty($updateRow->events)) {
                $decoded = json_decode((string) $updateRow->events, true);
                if (is_array($decoded)) {
                    $events = $decoded;
                }
            }

            $match = [
                'id' => (int) $record->id,
                'title' => $record->title,
                'league' => $record->league,
                'home_team' => $record->home_team,
                'away_team' => $record->away_team,
                'home_score' => $updateRow->home_score ?? $record->home_score,
                'away_score' => $updateRow->away_score ?? $record->away_score,
                'minute' => $updateRow->current_minute ?? null,
                'status' => $updateRow->status ?? $status,
                'match_date' => optional($record->match_date)->toIso8601String(),
                'events' => $events,
                'stadium' => $meta['location'] ?? null,
                'sport' => $meta['sport'] ?? null,
                'focus' => $meta['focus'] ?? null,
                'stream_url' => $meta['stream_url'] ?? null,
                'stream_links' => is_array($meta['stream_links'] ?? null) ? $meta['stream_links'] : [],
                'scout_name' => $meta['scout_name'] ?? null,
                'note' => $meta['note'] ?? null,
                'updated_at' => $updateRow->update_time ?? optional($record->updated_at)->toIso8601String(),
            ];

            return response()->json([
                'success' => true,
                'match' => $match,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mac bulunamadi',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mac detaylari alinirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function extractTeams(string $matchName): array
    {
        $parts = preg_split('/\s+[-:]\s+/', trim($matchName));
        if (is_array($parts) && count($parts) >= 2) {
            return [trim($parts[0]) ?: 'Ev Sahibi', trim($parts[1]) ?: 'Deplasman'];
        }

        return ['Ev Sahibi', 'Deplasman'];
    }

    private function encodeRoundMeta(?string $round, array $meta): ?string
    {
        $clean = array_filter($meta, fn ($value) => $value !== null && $value !== '');
        if (empty($clean) && $round) {
            return $round;
        }
        if (empty($clean)) {
            return null;
        }

        return 'meta::' . json_encode([
            'round' => $round,
            'meta' => $clean,
        ], JSON_UNESCAPED_UNICODE);
    }

    private function decodeRoundMeta(?string $round): array
    {
        if (!$round || !str_starts_with($round, 'meta::')) {
            return [];
        }

        $json = substr($round, 6);
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }

        $meta = $decoded['meta'] ?? [];
        return is_array($meta) ? $meta : [];
    }

    private function resolveScoutName(Request $request): ?string
    {
        $name = trim((string) $request->input('scout_name', ''));
        if ($name !== '') {
            return $name;
        }

        if (auth()->check()) {
            return (string) (auth()->user()->name ?? '');
        }

        return null;
    }
}

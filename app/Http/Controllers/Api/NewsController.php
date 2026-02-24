<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExternalNewsFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function __construct(private readonly ExternalNewsFeedService $externalFeed)
    {
    }

    public function live(): JsonResponse
    {
        $feedSource = (string) env('NEWS_FEED_SOURCE', 'Football Feed');

        $external = $this->externalFeed->fetch(5);
        if ($external !== []) {
            return response()->json([
                'ok' => true,
                'meta' => [
                    'feed_source' => $feedSource,
                ],
                'data' => $external,
            ]);
        }

        $internal = $this->fromOpportunities(5);
        if ($internal !== []) {
            return response()->json([
                'ok' => true,
                'meta' => [
                    'feed_source' => $feedSource,
                ],
                'data' => $internal,
            ]);
        }

        return response()->json([
            'ok' => true,
            'meta' => [
                'feed_source' => $feedSource,
            ],
            'data' => [[
                'id' => 0,
                'title' => 'Scout aginda yeni transfer firsatlari acildi.',
                'source' => 'NextScout',
                'published_at' => now()->toISOString(),
            ]],
        ]);
    }

    private function fromOpportunities(int $limit): array
    {
        $rows = DB::table('opportunities')
            ->leftJoin('users as teams', 'teams.id', '=', 'opportunities.team_user_id')
            ->where('opportunities.status', 'open')
            ->orderByDesc('opportunities.created_at')
            ->limit($limit)
            ->get([
                'opportunities.id',
                'opportunities.title',
                'opportunities.created_at as published_at',
                'teams.name as source',
            ]);

        if ($rows->isEmpty()) {
            return [];
        }

        return $rows->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'title' => (string) $row->title,
                'source' => $row->source ?: 'Kulup',
                'published_at' => (string) $row->published_at,
            ];
        })->values()->all();
    }
}

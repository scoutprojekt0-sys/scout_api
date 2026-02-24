<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function live(): JsonResponse
    {
        $rows = DB::table('opportunities')
            ->leftJoin('users as teams', 'teams.id', '=', 'opportunities.team_user_id')
            ->where('opportunities.status', 'open')
            ->orderByDesc('opportunities.created_at')
            ->limit(5)
            ->get([
                'opportunities.id',
                'opportunities.title',
                'opportunities.created_at as published_at',
                'teams.name as source',
            ]);

        if ($rows->isEmpty()) {
            return response()->json([
                'ok' => true,
                'data' => [[
                    'id' => 0,
                    'title' => 'Scout aginda yeni transfer firsatlari acildi.',
                    'source' => 'NextScout',
                    'published_at' => now()->toISOString(),
                ]],
            ]);
        }

        $data = $rows->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'title' => (string) $row->title,
                'source' => $row->source ?: 'Kulup',
                'published_at' => (string) $row->published_at,
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }
}

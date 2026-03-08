<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\ExternalNewsFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function live(Request $request): JsonResponse
    {
        try {
            $trOnly = filter_var($request->query('tr_only', false), FILTER_VALIDATE_BOOL);

            if ($trOnly) {
                $external = app(ExternalNewsFeedService::class)->fetchTurkey(10);

                return response()->json([
                    'ok' => true,
                    'data' => $external,
                    'source' => 'external_feed_tr_only',
                ]);
            }

            $news = News::where('is_published', true)
                ->orderByDesc('published_at')
                ->limit(10)
                ->get();

            if ($news->isEmpty()) {
                $external = app(ExternalNewsFeedService::class)->fetch(10);

                return response()->json([
                    'ok' => true,
                    'data' => $external,
                    'source' => 'external_feed',
                ]);
            }

            return response()->json([
                'ok' => true,
                'data' => $news,
                'source' => 'database',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Haberler yüklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'page' => ['nullable', 'integer', 'min:1'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);

            $perPage = (int) ($validated['per_page'] ?? 20);

            $news = News::where('is_published', true)
                ->orderByDesc('published_at')
                ->paginate($perPage);

            return response()->json([
                'ok' => true,
                'data' => $news->items(),
                'pagination' => [
                    'total' => $news->total(),
                    'per_page' => $news->perPage(),
                    'current_page' => $news->currentPage(),
                    'last_page' => $news->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Haberler yüklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
}

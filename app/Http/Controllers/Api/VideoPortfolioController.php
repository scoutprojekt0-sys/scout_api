<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerVideoPortfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoPortfolioController extends Controller
{
    public function index(Request $request, int $playerUserId): JsonResponse
    {
        $query = PlayerVideoPortfolio::query()
            ->where('player_user_id', $playerUserId);

        // Sadece kendi videoları değilse, public olanları göster
        if ($request->user()?->id !== $playerUserId) {
            $query->where('is_public', true);
        }

        $videos = $query->latest()->get();

        return response()->json([
            'ok' => true,
            'data' => $videos,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'video_url' => ['required', 'url', 'max:255'],
            'thumbnail_url' => ['nullable', 'url', 'max:255'],
            'video_type' => ['required', 'in:highlights,full_match,training,skills,goals'],
            'recorded_date' => ['nullable', 'date'],
            'is_public' => ['boolean'],
        ]);

        $video = PlayerVideoPortfolio::create([
            'player_user_id' => $request->user()->id,
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Video eklendi.',
            'data' => $video,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $video = PlayerVideoPortfolio::findOrFail($id);

        if ($video->player_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu videoyu düzenleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'video_type' => ['sometimes', 'in:highlights,full_match,training,skills,goals'],
            'is_public' => ['boolean'],
            'is_featured' => ['boolean'],
        ]);

        $video->update($validated);

        return response()->json([
            'ok' => true,
            'message' => 'Video güncellendi.',
            'data' => $video,
        ]);
    }

    public function delete(Request $request, int $id): JsonResponse
    {
        $video = PlayerVideoPortfolio::findOrFail($id);

        if ($video->player_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu videoyu silme yetkiniz yok.',
            ], 403);
        }

        $video->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Video silindi.',
        ]);
    }

    public function view(int $id): JsonResponse
    {
        $video = PlayerVideoPortfolio::findOrFail($id);
        $video->incrementViews();

        return response()->json([
            'ok' => true,
            'data' => $video,
        ]);
    }

    public function featured(Request $request): JsonResponse
    {
        $videos = PlayerVideoPortfolio::query()
            ->where('is_featured', true)
            ->where('is_public', true)
            ->with(['player'])
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $videos,
        ]);
    }
}

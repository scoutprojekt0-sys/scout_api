<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileView;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileViewController extends Controller
{
    public function track(Request $request, int $userId): JsonResponse
    {
        $viewerUserId = $request->user()?->id;

        // Kendi profilini görüntülemeyi kaydetme
        if ($viewerUserId === $userId) {
            return response()->json(['ok' => true]);
        }

        ProfileView::create([
            'viewer_user_id' => $viewerUserId,
            'viewed_user_id' => $userId,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Görüntüleme kaydedildi.',
        ]);
    }

    public function myViews(Request $request): JsonResponse
    {
        $views = ProfileView::query()
            ->where('viewed_user_id', $request->user()->id)
            ->with(['viewer'])
            ->latest('viewed_at')
            ->paginate(30);

        return response()->json([
            'ok' => true,
            'data' => $views,
        ]);
    }

    public function viewCount(Request $request, int $userId): JsonResponse
    {
        $count = ProfileView::query()
            ->where('viewed_user_id', $userId)
            ->count();

        return response()->json([
            'ok' => true,
            'data' => ['view_count' => $count],
        ]);
    }
}

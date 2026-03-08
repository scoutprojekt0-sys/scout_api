<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->with(['targetUser'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $favorites,
        ]);
    }

    public function toggle(Request $request, int $targetUserId): JsonResponse
    {
        $userId = $request->user()->id;

        if ($userId === $targetUserId) {
            return response()->json([
                'ok' => false,
                'message' => 'Kendinizi favorilere ekleyemezsiniz.',
            ], 400);
        }

        $favorite = Favorite::query()
            ->where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Favorilerden çıkarıldı.';
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $userId,
                'target_user_id' => $targetUserId,
            ]);
            $message = 'Favorilere eklendi.';
            $isFavorited = true;
        }

        return response()->json([
            'ok' => true,
            'message' => $message,
            'data' => ['is_favorited' => $isFavorited],
        ]);
    }

    public function check(Request $request, int $targetUserId): JsonResponse
    {
        $isFavorited = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('target_user_id', $targetUserId)
            ->exists();

        return response()->json([
            'ok' => true,
            'data' => ['is_favorited' => $isFavorited],
        ]);
    }
}

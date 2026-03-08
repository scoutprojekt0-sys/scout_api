<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    /**
     * Get community feed
     */
    public function getFeed(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = CommunityPost::with(['user', 'likes', 'comments'])
            ->where('visibility', 'public')
            ->orWhere(function ($q) use ($user) {
                if ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere(function ($inner) use ($user) {
                          $inner->where('visibility', 'followers')
                                ->whereIn('user_id', $user->following()->pluck('following_id'));
                      });
                }
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        $posts = $query->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $posts,
        ]);
    }

    /**
     * Create new post
     */
    public function createPost(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'type' => 'in:text,image,video,poll,share',
            'media' => 'nullable|array',
            'visibility' => 'in:public,followers,private',
        ]);

        $post = CommunityPost::create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'text',
            'media' => $validated['media'] ?? null,
            'visibility' => $validated['visibility'] ?? 'public',
        ]);

        // Award XP for post creation
        $this->awardXP($request->user(), 10, 'post_created');

        return response()->json([
            'ok' => true,
            'data' => $post->load('user'),
        ], 201);
    }

    /**
     * Like/Unlike post
     */
    public function toggleLike(Request $request, int $postId): JsonResponse
    {
        $user = $request->user();
        $post = CommunityPost::findOrFail($postId);

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            $action = 'unliked';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $post->increment('likes_count');
            $action = 'liked';

            // Award XP
            $this->awardXP($user, 2, 'post_liked');
        }

        return response()->json([
            'ok' => true,
            'action' => $action,
            'likes_count' => $post->fresh()->likes_count,
        ]);
    }

    /**
     * Award XP to user
     */
    private function awardXP($user, int $amount, string $action): void
    {
        DB::table('user_xp_logs')->insert([
            'user_id' => $user->id,
            'xp_amount' => $amount,
            'action_type' => $action,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->increment('xp_points', $amount);

        // Check level up
        $newLevel = floor(sqrt($user->xp_points / 100)) + 1;
        if ($newLevel > $user->level) {
            $user->update(['level' => $newLevel]);

            // Award coins on level up
            $user->increment('coins', $newLevel * 10);
        }
    }
}

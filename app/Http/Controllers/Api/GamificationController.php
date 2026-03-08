<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GamificationController extends Controller
{
    /**
     * Get user profile with gamification stats
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $achievements = $user->achievements()
            ->wherePivot('progress', 100)
            ->get();

        $stats = [
            'xp_points' => $user->xp_points,
            'level' => $user->level,
            'coins' => $user->coins,
            'next_level_xp' => pow($user->level, 2) * 100,
            'achievements_count' => $achievements->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'posts_count' => $user->communityPosts()->count(),
        ];

        return response()->json([
            'ok' => true,
            'data' => [
                'user' => $user,
                'stats' => $stats,
                'achievements' => $achievements,
            ],
        ]);
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $type = $request->get('type', 'xp'); // xp, level, coins

        $users = \App\Models\User::query()
            ->select(['id', 'name', 'role', 'xp_points', 'level', 'coins'])
            ->orderByDesc($type === 'xp' ? 'xp_points' : ($type === 'level' ? 'level' : 'coins'))
            ->limit(100)
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                return $user;
            });

        return response()->json([
            'ok' => true,
            'data' => [
                'type' => $type,
                'users' => $users,
            ],
        ]);
    }

    /**
     * Check and award achievements
     */
    public function checkAchievements(Request $request): JsonResponse
    {
        $user = $request->user();
        $newAchievements = [];

        // Example: First post achievement
        if ($user->communityPosts()->count() >= 1) {
            $achievement = Achievement::where('key', 'first_post')->first();
            if ($achievement && !$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                $user->achievements()->attach($achievement->id, [
                    'earned_at' => now(),
                    'progress' => 100,
                ]);
                $user->increment('xp_points', $achievement->points);
                $newAchievements[] = $achievement;
            }
        }

        // Example: Video master achievement
        if (Video::where('user_id', $user->id)->count() >= 10) {
            $achievement = Achievement::where('key', 'video_master')->first();
            if ($achievement && !$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                $user->achievements()->attach($achievement->id, [
                    'earned_at' => now(),
                    'progress' => 100,
                ]);
                $user->increment('xp_points', $achievement->points);
                $newAchievements[] = $achievement;
            }
        }

        return response()->json([
            'ok' => true,
            'new_achievements' => $newAchievements,
        ]);
    }

    /**
     * Use referral code
     */
    public function useReferralCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:users,referral_code',
        ]);

        $referrer = \App\Models\User::where('referral_code', $validated['code'])->first();
        $user = $request->user();

        if (!$referrer || $referrer->id === $user->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Geçersiz referans kodu.',
            ], 400);
        }

        // Check if already used a referral
        if (DB::table('referrals')->where('referred_id', $user->id)->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'Referans kodu zaten kullanılmış.',
            ], 400);
        }

        // Create referral
        DB::table('referrals')->insert([
            'referrer_id' => $referrer->id,
            'referred_id' => $user->id,
            'referral_code' => $validated['code'],
            'reward_xp' => 100,
            'reward_coins' => 50,
            'reward_claimed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Award both users
        $referrer->increment('xp_points', 100);
        $referrer->increment('coins', 50);
        $user->increment('xp_points', 50);
        $user->increment('coins', 25);

        return response()->json([
            'ok' => true,
            'message' => 'Referans kodu başarıyla kullanıldı!',
        ]);
    }
}

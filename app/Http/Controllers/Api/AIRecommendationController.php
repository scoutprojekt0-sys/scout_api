<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIRecommendationController extends Controller
{
    /**
     * Get AI recommendations for user
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get user preferences
        $preferences = DB::table('user_preferences')
            ->where('user_id', $user->id)
            ->first();

        // Deterministic scoring until ML model is added
        $recommendedPlayers = $this->getRecommendedPlayers($user, $preferences);

        // Log recommendations
        foreach ($recommendedPlayers as $player) {
            DB::table('ai_recommendations')->insert([
                'user_id' => $user->id,
                'recommendable_type' => 'App\Models\User',
                'recommendable_id' => $player['id'],
                'score' => $player['score'],
                'factors' => json_encode($player['factors']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'ok' => true,
            'data' => $recommendedPlayers,
        ]);
    }

    /**
     * Simple player recommendation logic
     */
    private function getRecommendedPlayers($user, $preferences): array
    {
        $query = \App\Models\User::where('role', 'player')
            ->where('id', '!=', $user->id)
            ->limit(10);

        // Apply preference filters if they exist
        if ($preferences && $preferences->preferred_positions) {
            $positions = json_decode($preferences->preferred_positions, true);
            // Would filter by position if we had that field
        }

        $players = $query->get();

        return $players->map(function ($player) use ($user, $preferences) {
            $activityScore = $this->computeActivityScore((int) $player->id);
            $engagementScore = $this->computeEngagementScore((int) $player->id);
            $compatibilityScore = $this->computeCompatibilityScore($player, $preferences);
            $composite = round((($activityScore * 0.35) + ($engagementScore * 0.30) + ($compatibilityScore * 0.35)) / 100, 4);

            return [
                'id' => $player->id,
                'name' => $player->name,
                'role' => $player->role,
                'score' => $composite,
                'factors' => [
                    'activity' => $activityScore,
                    'engagement' => $engagementScore,
                    'compatibility' => $compatibilityScore,
                ],
            ];
        })->sortByDesc('score')->values()->toArray();
    }

    /**
     * Save user preferences
     */
    public function savePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'preferred_positions' => 'nullable|array',
            'preferred_leagues' => 'nullable|array',
            'preferred_countries' => 'nullable|array',
            'age_range' => 'nullable|array',
            'budget_range' => 'nullable|array',
        ]);

        DB::table('user_preferences')->updateOrInsert(
            ['user_id' => $request->user()->id],
            array_merge($validated, [
                'updated_at' => now(),
                'created_at' => now(),
            ])
        );

        return response()->json([
            'ok' => true,
            'message' => 'Tercihler kaydedildi',
        ]);
    }

    /**
     * Track recommendation interaction
     */
    public function trackInteraction(Request $request, int $recommendationId): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:click,save,dismiss',
        ]);

        $updates = ['updated_at' => now()];
        if ($validated['action'] === 'click') {
            $updates['clicked'] = true;
        } elseif ($validated['action'] === 'save') {
            $updates['saved'] = true;
        } elseif ($validated['action'] === 'dismiss') {
            $updates['clicked'] = false;
            $updates['saved'] = false;
        }

        DB::table('ai_recommendations')
            ->where('id', $recommendationId)
            ->where('user_id', $request->user()->id)
            ->update($updates);

        return response()->json(['ok' => true]);
    }

    private function computeActivityScore(int $playerUserId): int
    {
        $profileViews = (int) DB::table('profile_views')->where('viewed_user_id', $playerUserId)->count();
        $mediaCount = (int) DB::table('media')->where('user_id', $playerUserId)->count();
        $score = 45 + min(35, (int) floor($profileViews / 10)) + min(20, $mediaCount * 2);
        return max(0, min(100, $score));
    }

    private function computeEngagementScore(int $playerUserId): int
    {
        $favorites = (int) DB::table('favorites')->where('target_user_id', $playerUserId)->count();
        $messages = (int) DB::table('player_messages')->where('to_user_id', $playerUserId)->count();
        $score = 40 + min(35, $favorites * 3) + min(25, (int) floor($messages / 2));
        return max(0, min(100, $score));
    }

    private function computeCompatibilityScore($player, $preferences): int
    {
        $base = 65;
        $seed = abs(crc32((string) ($player->id . '|' . ($player->name ?? '')))) % 26;
        $score = $base + $seed;

        if ($preferences && !empty($preferences->preferred_positions)) {
            $score += 3;
        }

        return max(0, min(100, $score));
    }
}

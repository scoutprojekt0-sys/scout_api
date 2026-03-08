<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * User profil bilgilerini cache'le
     */
    public static function getUserProfile(int $userId, string $role = null)
    {
        $cacheKey = "user.profile.{$userId}";

        return Cache::remember($cacheKey, 3600, function () use ($userId, $role) {
            $user = \App\Models\User::find($userId);

            if (!$user) {
                return null;
            }

            // Role'e göre ilgili profili yükle
            switch ($user->role ?? $role) {
                case 'player':
                    $user->load('playerProfile', 'media');
                    break;
                case 'team':
                    $user->load('teamProfile');
                    break;
                case 'scout':
                case 'manager':
                case 'coach':
                    $user->load('staffProfile');
                    break;
            }

            return $user;
        });
    }

    /**
     * User profile cache'ini temizle
     */
    public static function forgetUserProfile(int $userId): void
    {
        Cache::forget("user.profile.{$userId}");
    }

    /**
     * Opportunities listesini cache'le
     */
    public static function getOpenOpportunities(int $page = 1, int $perPage = 20)
    {
        $cacheKey = "opportunities.open.page.{$page}";

        return Cache::remember($cacheKey, 600, function () use ($page, $perPage) {
            return \App\Models\Opportunity::query()
                ->where('status', 'open')
                ->with('team')
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /**
     * Opportunities cache'ini temizle
     */
    public static function forgetOpportunities(): void
    {
        // Tüm opportunities cache'lerini temizle
        Cache::flush(); // Veya daha spesifik tag-based cache kullan
    }

    /**
     * Player istatistiklerini cache'le
     */
    public static function getPlayerStatistics(int $playerUserId)
    {
        $cacheKey = "player.statistics.{$playerUserId}";

        return Cache::remember($cacheKey, 1800, function () use ($playerUserId) {
            return \App\Models\PlayerStatistic::query()
                ->where('player_user_id', $playerUserId)
                ->orderBy('season', 'desc')
                ->get();
        });
    }

    /**
     * Player statistics cache'ini temizle
     */
    public static function forgetPlayerStatistics(int $playerUserId): void
    {
        Cache::forget("player.statistics.{$playerUserId}");
    }

    /**
     * Unread notifications count cache'le
     */
    public static function getUnreadNotificationsCount(int $userId): int
    {
        $cacheKey = "user.{$userId}.unread_notifications";

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            return \App\Models\Notification::query()
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->count();
        });
    }

    /**
     * Unread notifications cache'ini temizle
     */
    public static function forgetUnreadNotifications(int $userId): void
    {
        Cache::forget("user.{$userId}.unread_notifications");
    }
}

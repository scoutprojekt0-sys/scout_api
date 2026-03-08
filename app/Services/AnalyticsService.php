<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Track page view
     */
    public function trackPageView(array $data)
    {
        DB::table('page_views')->insert([
            'user_id' => auth()->id(),
            'page_type' => $data['page_type'],
            'page_id' => $data['page_id'],
            'session_id' => $data['session_id'] ?? session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'viewed_at' => now(),
        ]);

        // Update session page view count
        $this->updateSession($data['session_id'] ?? session()->getId());
    }

    /**
     * Track custom event
     */
    public function trackEvent(string $eventName, array $data = [])
    {
        $userAgent = $this->parseUserAgent(request()->userAgent());

        DB::table('analytics_events')->insert([
            'user_id' => auth()->id(),
            'event_name' => $eventName,
            'event_category' => $data['category'] ?? null,
            'event_action' => $data['action'] ?? null,
            'event_label' => $data['label'] ?? null,
            'event_value' => $data['value'] ?? null,
            'page_url' => $data['page_url'] ?? request()->url(),
            'page_title' => $data['page_title'] ?? null,
            'referrer' => request()->header('referer'),
            'utm_source' => request()->get('utm_source'),
            'utm_medium' => request()->get('utm_medium'),
            'utm_campaign' => request()->get('utm_campaign'),
            'utm_term' => request()->get('utm_term'),
            'utm_content' => request()->get('utm_content'),
            'device_type' => $userAgent['device_type'],
            'browser' => $userAgent['browser'],
            'os' => $userAgent['os'],
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'ip_address' => request()->ip(),
            'custom_data' => isset($data['custom']) ? json_encode($data['custom']) : null,
            'created_at' => now(),
        ]);
    }

    /**
     * Track conversion
     */
    public function trackConversion(User $user, string $type, array $data = [])
    {
        DB::table('conversions')->insert([
            'user_id' => $user->id,
            'conversion_type' => $type,
            'conversion_value' => $data['value'] ?? null,
            'revenue' => $data['revenue'] ?? null,
            'source' => $data['source'] ?? $this->getTrafficSource(),
            'campaign' => request()->get('utm_campaign'),
            'attribution_data' => isset($data['attribution']) ? json_encode($data['attribution']) : null,
            'converted_at' => now(),
        ]);
    }

    /**
     * Update user session
     */
    protected function updateSession(string $sessionId)
    {
        $session = DB::table('user_sessions')->where('session_id', $sessionId)->first();

        if (!$session) {
            $userAgent = $this->parseUserAgent(request()->userAgent());

            DB::table('user_sessions')->insert([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => $userAgent['device_type'],
                'browser' => $userAgent['browser'],
                'os' => $userAgent['os'],
                'page_views' => 1,
                'landing_page' => request()->url(),
                'started_at' => now(),
            ]);
        } else {
            DB::table('user_sessions')
                ->where('session_id', $sessionId)
                ->update([
                    'page_views' => DB::raw('page_views + 1'),
                    'exit_page' => request()->url(),
                    'ended_at' => now(),
                    'duration' => now()->diffInSeconds($session->started_at),
                ]);
        }
    }

    /**
     * Get traffic source
     */
    protected function getTrafficSource(): string
    {
        $referrer = request()->header('referer');

        if (!$referrer) {
            return 'direct';
        }

        if (str_contains($referrer, 'google.com')) {
            return 'organic_google';
        }

        if (str_contains($referrer, 'facebook.com') || str_contains($referrer, 'fb.com')) {
            return 'social_facebook';
        }

        if (str_contains($referrer, 'twitter.com') || str_contains($referrer, 't.co')) {
            return 'social_twitter';
        }

        if (str_contains($referrer, 'instagram.com')) {
            return 'social_instagram';
        }

        return 'referral';
    }

    /**
     * Parse user agent
     */
    protected function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'browser' => 'unknown',
                'os' => 'unknown',
            ];
        }

        // Simple parsing - you can use a library like Mobile_Detect for better results
        $deviceType = 'desktop';
        if (preg_match('/(android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini)/i', $userAgent)) {
            $deviceType = preg_match('/(ipad|tablet)/i', $userAgent) ? 'tablet' : 'mobile';
        }

        // Browser detection
        $browser = 'unknown';
        if (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
        elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/Edge/i', $userAgent)) $browser = 'Edge';
        elseif (preg_match('/MSIE|Trident/i', $userAgent)) $browser = 'IE';

        // OS detection
        $os = 'unknown';
        if (preg_match('/Windows/i', $userAgent)) $os = 'Windows';
        elseif (preg_match('/Mac OS X/i', $userAgent)) $os = 'macOS';
        elseif (preg_match('/Linux/i', $userAgent)) $os = 'Linux';
        elseif (preg_match('/Android/i', $userAgent)) $os = 'Android';
        elseif (preg_match('/iOS|iPhone|iPad/i', $userAgent)) $os = 'iOS';

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }

    /**
     * Get dashboard stats
     */
    public function getDashboardStats(string $period = 'today')
    {
        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfDay(),
        };

        $endDate = match($period) {
            'yesterday' => now()->subDay()->endOfDay(),
            default => now(),
        };

        return [
            'page_views' => DB::table('page_views')
                ->whereBetween('viewed_at', [$startDate, $endDate])
                ->count(),

            'unique_visitors' => DB::table('page_views')
                ->whereBetween('viewed_at', [$startDate, $endDate])
                ->distinct('session_id')
                ->count('session_id'),

            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),

            'avg_session_duration' => DB::table('user_sessions')
                ->whereBetween('started_at', [$startDate, $endDate])
                ->avg('duration'),

            'bounce_rate' => $this->calculateBounceRate($startDate, $endDate),

            'top_pages' => DB::table('page_views')
                ->select('page_type', DB::raw('count(*) as views'))
                ->whereBetween('viewed_at', [$startDate, $endDate])
                ->groupBy('page_type')
                ->orderByDesc('views')
                ->limit(10)
                ->get(),

            'traffic_sources' => DB::table('user_sessions')
                ->select(
                    DB::raw("CASE
                        WHEN landing_page LIKE '%utm_source=%' THEN 'paid'
                        WHEN landing_page IS NULL THEN 'direct'
                        ELSE 'referral'
                    END as source"),
                    DB::raw('count(*) as sessions')
                )
                ->whereBetween('started_at', [$startDate, $endDate])
                ->groupBy('source')
                ->get(),

            'device_breakdown' => DB::table('user_sessions')
                ->select('device_type', DB::raw('count(*) as sessions'))
                ->whereBetween('started_at', [$startDate, $endDate])
                ->groupBy('device_type')
                ->get(),
        ];
    }

    /**
     * Calculate bounce rate
     */
    protected function calculateBounceRate($startDate, $endDate): float
    {
        $totalSessions = DB::table('user_sessions')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->count();

        if ($totalSessions === 0) {
            return 0;
        }

        $bouncedSessions = DB::table('user_sessions')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->where('page_views', 1)
            ->count();

        return round(($bouncedSessions / $totalSessions) * 100, 2);
    }

    /**
     * Log error
     */
    public function logError(string $type, string $message, array $context = [])
    {
        $existingError = DB::table('error_logs')
            ->where('error_message', $message)
            ->where('url', request()->url())
            ->where('first_seen_at', '>=', now()->subHour())
            ->first();

        if ($existingError) {
            DB::table('error_logs')
                ->where('id', $existingError->id)
                ->update([
                    'count' => DB::raw('count + 1'),
                    'last_seen_at' => now(),
                ]);
        } else {
            DB::table('error_logs')->insert([
                'user_id' => auth()->id(),
                'error_type' => $type,
                'error_message' => $message,
                'stack_trace' => $context['stack_trace'] ?? null,
                'file_path' => $context['file'] ?? null,
                'line_number' => $context['line'] ?? null,
                'url' => request()->url(),
                'user_agent' => request()->userAgent(),
                'context' => json_encode($context),
                'count' => 1,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);
        }
    }
}

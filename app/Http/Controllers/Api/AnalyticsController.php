<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    protected $analytics;

    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * Track page view
     */
    public function trackPageView(Request $request)
    {
        $request->validate([
            'page_type' => 'required|string',
            'page_id' => 'nullable|integer',
            'session_id' => 'required|string',
        ]);

        $this->analytics->trackPageView($request->all());

        return response()->json(['ok' => true]);
    }

    /**
     * Track custom event
     */
    public function trackEvent(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string',
        ]);

        $this->analytics->trackEvent($request->event_name, $request->except('event_name'));

        return response()->json(['ok' => true]);
    }

    /**
     * Track error
     */
    public function trackError(Request $request)
    {
        $request->validate([
            'error_type' => 'required|string',
            'error_message' => 'required|string',
        ]);

        $this->analytics->logError(
            $request->error_type,
            $request->error_message,
            $request->except(['error_type', 'error_message'])
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Track performance metric
     */
    public function trackPerformance(Request $request)
    {
        $request->validate([
            'metric_type' => 'required|string',
            'duration' => 'required|integer',
            'url' => 'required|string',
        ]);

        DB::table('performance_metrics')->insert([
            'url' => $request->url,
            'metric_type' => $request->metric_type,
            'duration' => $request->duration,
            'browser' => $request->browser,
            'device_type' => $request->device_type,
            'additional_data' => $request->additional_data ? json_encode($request->additional_data) : null,
            'measured_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Get dashboard analytics
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'today');
        $stats = $this->analytics->getDashboardStats($period);

        return response()->json([
            'ok' => true,
            'data' => $stats,
            'period' => $period,
        ]);
    }

    /**
     * Get user statistics
     */
    public function userStats(Request $request)
    {
        $period = $request->get('period', 'month');

        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $data = [
            'total_users' => DB::table('users')->count(),
            'new_users' => DB::table('users')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'active_users' => DB::table('user_sessions')
                ->where('started_at', '>=', $startDate)
                ->distinct('user_id')
                ->count('user_id'),
            'verified_users' => DB::table('users')
                ->whereNotNull('email_verified_at')
                ->count(),
            'user_growth' => $this->calculateUserGrowth($startDate),
            'retention_rate' => $this->calculateRetentionRate($startDate),
            'top_countries' => DB::table('user_sessions')
                ->select('country', DB::raw('count(distinct user_id) as users'))
                ->where('started_at', '>=', $startDate)
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderByDesc('users')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get revenue statistics
     */
    public function revenueStats(Request $request)
    {
        $period = $request->get('period', 'month');

        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $data = [
            'total_revenue' => DB::table('payments')
                ->where('status', 'completed')
                ->where('paid_at', '>=', $startDate)
                ->sum('amount'),
            'total_payments' => DB::table('payments')
                ->where('status', 'completed')
                ->where('paid_at', '>=', $startDate)
                ->count(),
            'mrr' => $this->calculateMRR(),
            'arr' => $this->calculateARR(),
            'average_transaction' => DB::table('payments')
                ->where('status', 'completed')
                ->where('paid_at', '>=', $startDate)
                ->avg('amount'),
            'active_subscriptions' => DB::table('subscriptions')
                ->where('status', 'active')
                ->count(),
            'churn_rate' => $this->calculateChurnRate($startDate),
            'revenue_by_plan' => DB::table('payments')
                ->join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
                ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
                ->select('subscription_plans.name', DB::raw('sum(payments.amount) as revenue'))
                ->where('payments.status', 'completed')
                ->where('payments.paid_at', '>=', $startDate)
                ->groupBy('subscription_plans.name')
                ->get(),
        ];

        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }

    /**
     * Calculate user growth rate
     */
    protected function calculateUserGrowth($startDate): float
    {
        $currentPeriod = DB::table('users')
            ->where('created_at', '>=', $startDate)
            ->count();

        $previousStart = (clone $startDate)->sub(now()->diff($startDate));
        $previousPeriod = DB::table('users')
            ->whereBetween('created_at', [$previousStart, $startDate])
            ->count();

        if ($previousPeriod === 0) {
            return 0;
        }

        return round((($currentPeriod - $previousPeriod) / $previousPeriod) * 100, 2);
    }

    /**
     * Calculate retention rate
     */
    protected function calculateRetentionRate($startDate): float
    {
        $newUsers = DB::table('users')
            ->where('created_at', '>=', $startDate)
            ->pluck('id');

        if ($newUsers->isEmpty()) {
            return 0;
        }

        $returnedUsers = DB::table('user_sessions')
            ->whereIn('user_id', $newUsers)
            ->where('started_at', '>', $startDate->copy()->addWeek())
            ->distinct('user_id')
            ->count('user_id');

        return round(($returnedUsers / $newUsers->count()) * 100, 2);
    }

    /**
     * Calculate Monthly Recurring Revenue (MRR)
     */
    protected function calculateMRR(): float
    {
        return DB::table('subscriptions')
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('subscriptions.status', 'active')
            ->sum(DB::raw('
                CASE
                    WHEN subscription_plans.billing_cycle = "monthly" THEN subscription_plans.price
                    WHEN subscription_plans.billing_cycle = "yearly" THEN subscription_plans.price / 12
                    ELSE 0
                END
            '));
    }

    /**
     * Calculate Annual Recurring Revenue (ARR)
     */
    protected function calculateARR(): float
    {
        return $this->calculateMRR() * 12;
    }

    /**
     * Calculate churn rate
     */
    protected function calculateChurnRate($startDate): float
    {
        $startSubscriptions = DB::table('subscriptions')
            ->where('starts_at', '<=', $startDate)
            ->where('status', 'active')
            ->count();

        if ($startSubscriptions === 0) {
            return 0;
        }

        $churnedSubscriptions = DB::table('subscriptions')
            ->where('cancelled_at', '>=', $startDate)
            ->count();

        return round(($churnedSubscriptions / $startSubscriptions) * 100, 2);
    }
}

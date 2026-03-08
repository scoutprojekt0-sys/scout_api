<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Get all subscription plans
     */
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Get current user's subscription
     */
    public function current(Request $request)
    {
        $subscription = $request->user()
            ->subscriptions()
            ->with('plan')
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json([
                'ok' => true,
                'data' => null,
                'message' => 'No active subscription',
            ]);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'subscription' => $subscription,
                'plan' => $subscription->plan,
                'is_active' => $subscription->isActive(),
                'on_trial' => $subscription->onTrial(),
                'days_remaining' => $subscription->daysRemaining(),
            ],
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method_id' => 'required_if:plan_id,!=,1', // Not required for free plan
        ]);

        $user = $request->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has active subscription
        $existingSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return response()->json([
                'ok' => false,
                'message' => 'You already have an active subscription',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Free plan
            if ($plan->isFree()) {
                $subscription = $user->subscriptions()->create([
                    'subscription_plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addYear(), // Free forever
                ]);

                DB::commit();

                return response()->json([
                    'ok' => true,
                    'data' => $subscription->load('plan'),
                    'message' => 'Successfully subscribed to free plan',
                ]);
            }

            // Paid plan - Use Stripe
            $stripeSubscription = $this->stripeService->createSubscription(
                $user,
                $plan,
                $request->payment_method_id
            );

            $subscription = $user->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $plan->billing_cycle === 'monthly'
                    ? now()->addMonth()
                    : now()->addYear(),
            ]);

            DB::commit();

            return response()->json([
                'ok' => true,
                'data' => $subscription->load('plan'),
                'message' => 'Successfully subscribed',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => 'Subscription failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $subscription = $request->user()
            ->subscriptions()
            ->where('status', 'active')
            ->firstOrFail();

        // Cancel on Stripe if paid subscription
        if ($subscription->stripe_subscription_id) {
            $this->stripeService->cancelSubscription($subscription->stripe_subscription_id);
        }

        $subscription->cancel();

        return response()->json([
            'ok' => true,
            'message' => 'Subscription cancelled successfully',
        ]);
    }

    /**
     * Resume cancelled subscription
     */
    public function resume(Request $request)
    {
        $subscription = $request->user()
            ->subscriptions()
            ->where('status', 'cancelled')
            ->firstOrFail();

        // Can only resume if not expired yet
        if ($subscription->hasExpired()) {
            return response()->json([
                'ok' => false,
                'message' => 'Cannot resume expired subscription',
            ], 400);
        }

        // Resume on Stripe if paid subscription
        if ($subscription->stripe_subscription_id) {
            $this->stripeService->resumeSubscription($subscription->stripe_subscription_id);
        }

        $subscription->resume();

        return response()->json([
            'ok' => true,
            'message' => 'Subscription resumed successfully',
        ]);
    }

    /**
     * Upgrade/Downgrade plan
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = $request->user();
        $newPlan = SubscriptionPlan::findOrFail($request->plan_id);

        $currentSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->firstOrFail();

        // Update on Stripe if paid subscription
        if ($currentSubscription->stripe_subscription_id) {
            $this->stripeService->updateSubscription(
                $currentSubscription->stripe_subscription_id,
                $newPlan
            );
        }

        $currentSubscription->update([
            'subscription_plan_id' => $newPlan->id,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $currentSubscription->fresh()->load('plan'),
            'message' => 'Plan updated successfully',
        ]);
    }

    /**
     * Get subscription usage stats
     */
    public function usage(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscriptions()
            ->with('plan')
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json([
                'ok' => false,
                'message' => 'No active subscription',
            ], 404);
        }

        $usage = DB::table('subscription_usage')
            ->where('user_id', $user->id)
            ->where('usage_date', today())
            ->first();

        $plan = $subscription->plan;

        return response()->json([
            'ok' => true,
            'data' => [
                'plan' => $plan->name,
                'profile_views' => [
                    'used' => $usage->profile_views_count ?? 0,
                    'limit' => $plan->profile_views_limit,
                    'remaining' => max(0, $plan->profile_views_limit - ($usage->profile_views_count ?? 0)),
                ],
                'messages' => [
                    'used' => $usage->messages_sent_count ?? 0,
                    'limit' => $plan->messages_limit,
                    'remaining' => max(0, $plan->messages_limit - ($usage->messages_sent_count ?? 0)),
                ],
                'video_views' => [
                    'used' => $usage->video_views_count ?? 0,
                    'limit' => $plan->video_views_limit,
                    'remaining' => max(0, $plan->video_views_limit - ($usage->video_views_count ?? 0)),
                ],
            ],
        ]);
    }
}

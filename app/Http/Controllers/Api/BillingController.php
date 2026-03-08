<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function __construct(
        private StripeService $stripeService,
        private PayPalService $paypalService
    ) {}

    public function plans(): JsonResponse
    {
        $plans = DB::table('subscription_plans')
            ->where('active', true)
            ->orderBy('price')
            ->get();

        return response()->json($plans);
    }

    public function currentSubscription(): JsonResponse
    {
        $user = auth()->user();

        $subscription = DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        return response()->json($subscription ?: ['status' => 'none']);
    }

    public function subscribe(): JsonResponse
    {
        $validated = request()->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|in:stripe,paypal',
        ]);

        $user = auth()->user();
        $plan = DB::table('subscription_plans')->find($validated['plan_id']);

        try {
            if ($validated['payment_method'] === 'stripe') {
                $result = $this->stripeService->createSubscription($user, $plan);
            } else {
                $result = $this->paypalService->createSubscription($user, $plan);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancel(): JsonResponse
    {
        $user = auth()->user();

        DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return response()->json(['message' => 'Subscription cancelled successfully']);
    }

    public function payments(): JsonResponse
    {
        $user = auth()->user();

        $payments = DB::table('payments')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($payments);
    }

    public function invoices(): JsonResponse
    {
        $user = auth()->user();

        $invoices = DB::table('invoices')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($invoices);
    }
}

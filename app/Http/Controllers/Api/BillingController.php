<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    public function plans(): JsonResponse
    {
        $this->ensureDefaultPlans();

        return response()->json([
            'ok' => true,
            'data' => SubscriptionPlan::query()
                ->active()
                ->ordered()
                ->get(),
        ]);
    }

    public function currentSubscription(Request $request): JsonResponse
    {
        $subscription = Subscription::query()
            ->with('plan')
            ->where('user_id', (int) $request->user()->id)
            ->whereIn('status', ['active', 'trialing'])
            ->latest('id')
            ->first();

        return response()->json([
            'ok' => true,
            'data' => $subscription,
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'provider' => ['nullable', Rule::in(['stripe', 'paypal'])],
            'payment_method_token' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $plan = SubscriptionPlan::query()->findOrFail((int) $validated['plan_id']);
        $provider = $validated['provider'] ?? 'stripe';

        $result = DB::transaction(function () use ($user, $plan, $provider) {
            Subscription::query()
                ->where('user_id', (int) $user->id)
                ->whereIn('status', ['active', 'trialing'])
                ->update([
                    'status' => 'cancelled',
                    'canceled_at' => now(),
                    'updated_at' => now(),
                ]);

            $periodStart = now();
            $periodEnd = $plan->interval === 'year' ? now()->addYear() : now()->addMonth();

            $subscription = Subscription::query()->create([
                'user_id' => (int) $user->id,
                'subscription_plan_id' => (int) $plan->id,
                'stripe_subscription_id' => strtoupper($provider) . '_SUB_' . now()->format('YmdHis') . '_' . $user->id,
                'status' => 'active',
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'cancel_at_period_end' => false,
            ]);

            $payment = Payment::query()->create([
                'user_id' => (int) $user->id,
                'subscription_id' => (int) $subscription->id,
                'stripe_payment_intent_id' => strtoupper($provider) . '_PAY_' . now()->format('YmdHis') . '_' . $user->id,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'status' => 'succeeded',
                'description' => $plan->name . ' abonelik odemesi',
                'metadata' => [
                    'provider' => $provider,
                    'mode' => 'local-sandbox',
                ],
            ]);

            $invoice = Invoice::query()->create([
                'user_id' => (int) $user->id,
                'subscription_id' => (int) $subscription->id,
                'stripe_invoice_id' => strtoupper($provider) . '_INV_' . now()->format('YmdHis') . '_' . $user->id,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'status' => 'paid',
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ]);

            $user->update([
                'subscription_status' => 'active',
                'subscription_end_date' => $periodEnd,
            ]);

            return compact('subscription', 'payment', 'invoice');
        });

        return response()->json([
            'ok' => true,
            'message' => 'Abonelik baslatildi.',
            'data' => [
                'subscription' => $result['subscription']->load('plan'),
                'payment' => $result['payment'],
                'invoice' => $result['invoice'],
            ],
        ], 201);
    }

    public function cancel(Request $request): JsonResponse
    {
        $subscription = Subscription::query()
            ->where('user_id', (int) $request->user()->id)
            ->whereIn('status', ['active', 'trialing'])
            ->latest('id')
            ->first();

        if (!$subscription) {
            return response()->json([
                'ok' => false,
                'message' => 'Aktif abonelik bulunamadi.',
            ], 404);
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancel_at_period_end' => true,
            'canceled_at' => now(),
        ]);

        $request->user()->update([
            'subscription_status' => 'cancelled',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Abonelik iptal edildi.',
            'data' => $subscription->fresh(),
        ]);
    }

    public function payments(Request $request): JsonResponse
    {
        $payments = Payment::query()
            ->where('user_id', (int) $request->user()->id)
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'ok' => true,
            'data' => $payments,
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $invoices = Invoice::query()
            ->where('user_id', (int) $request->user()->id)
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'ok' => true,
            'data' => $invoices,
        ]);
    }

    private function ensureDefaultPlans(): void
    {
        if (SubscriptionPlan::query()->exists()) {
            return;
        }

        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Temel profil ve sinirli kesif ozellikleri.',
                'price' => 0,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['1 profil', 'temel basvuru'],
                'max_users' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'Scout Pro',
                'slug' => 'scout-pro',
                'description' => 'Scout icin gelismis filtreleme ve analiz.',
                'price' => 19.99,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['gelismis filtre', 'sinirsiz favori'],
                'max_users' => 1,
                'sort_order' => 2,
            ],
            [
                'name' => 'Manager Pro',
                'slug' => 'manager-pro',
                'description' => 'Menajerler icin kontrat ve oyuncu havuzu yonetimi.',
                'price' => 49.99,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['kontrat takibi', 'premium kesif'],
                'max_users' => 3,
                'sort_order' => 3,
            ],
            [
                'name' => 'Club Premium',
                'slug' => 'club-premium',
                'description' => 'Kulup ekipleri icin coklu kullanici ve raporlama.',
                'price' => 149.99,
                'currency' => 'USD',
                'interval' => 'month',
                'features' => ['takim paneli', 'gelismis raporlar', 'oncelikli destek'],
                'max_users' => 20,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::query()->create($plan);
        }
    }
}

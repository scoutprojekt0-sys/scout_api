<?php

namespace App\Services;

use App\Models\User;
use App\Models\SubscriptionPlan;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create or get Stripe customer
     */
    public function getOrCreateCustomer(User $user)
    {
        if ($user->stripe_customer_id) {
            return Customer::retrieve($user->stripe_customer_id);
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    /**
     * Create subscription
     */
    public function createSubscription(User $user, SubscriptionPlan $plan, string $paymentMethodId)
    {
        $customer = $this->getOrCreateCustomer($user);

        // Attach payment method to customer
        $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $customer->id]);

        // Set as default payment method
        Customer::update($customer->id, [
            'invoice_settings' => [
                'default_payment_method' => $paymentMethodId,
            ],
        ]);

        // Create price ID mapping (you need to create these in Stripe Dashboard)
        $priceId = $this->getPriceId($plan);

        // Create subscription
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [
                ['price' => $priceId],
            ],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        return $subscription;
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(string $subscriptionId)
    {
        return Subscription::update($subscriptionId, [
            'cancel_at_period_end' => true,
        ]);
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(string $subscriptionId)
    {
        return Subscription::update($subscriptionId, [
            'cancel_at_period_end' => false,
        ]);
    }

    /**
     * Update subscription
     */
    public function updateSubscription(string $subscriptionId, SubscriptionPlan $newPlan)
    {
        $subscription = Subscription::retrieve($subscriptionId);
        $priceId = $this->getPriceId($newPlan);

        return Subscription::update($subscriptionId, [
            'items' => [
                [
                    'id' => $subscription->items->data[0]->id,
                    'price' => $priceId,
                ],
            ],
            'proration_behavior' => 'create_prorations',
        ]);
    }

    /**
     * Create payment intent
     */
    public function createPaymentIntent(float $amount, string $currency = 'usd', array $metadata = [])
    {
        return PaymentIntent::create([
            'amount' => $amount * 100, // Convert to cents
            'currency' => $currency,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Refund payment
     */
    public function refundPayment(string $paymentIntentId, ?float $amount = null)
    {
        $params = ['payment_intent' => $paymentIntentId];

        if ($amount) {
            $params['amount'] = $amount * 100;
        }

        return \Stripe\Refund::create($params);
    }

    /**
     * Get Stripe price ID for plan
     */
    protected function getPriceId(SubscriptionPlan $plan): string
    {
        // Map plan slug to Stripe Price ID
        // You need to create these prices in Stripe Dashboard first
        $priceMapping = [
            'scout-pro-monthly' => config('services.stripe.prices.scout_pro_monthly'),
            'scout-pro-yearly' => config('services.stripe.prices.scout_pro_yearly'),
            'manager-pro-monthly' => config('services.stripe.prices.manager_pro_monthly'),
            'manager-pro-yearly' => config('services.stripe.prices.manager_pro_yearly'),
            'club-premium-monthly' => config('services.stripe.prices.club_premium_monthly'),
            'club-premium-yearly' => config('services.stripe.prices.club_premium_yearly'),
        ];

        $key = $plan->slug . '-' . $plan->billing_cycle;

        if (!isset($priceMapping[$key])) {
            throw new \Exception('Price ID not found for plan: ' . $key);
        }

        return $priceMapping[$key];
    }

    /**
     * Handle webhook event
     */
    public function handleWebhook(array $payload, string $signature)
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                json_encode($payload),
                $signature,
                $webhookSecret
            );
        } catch (\Exception $e) {
            throw new \Exception('Webhook signature verification failed');
        }

        // Handle different event types
        switch ($event->type) {
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;
        }

        return true;
    }

    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => $stripeSubscription->status === 'active' ? 'active' : 'cancelled',
            ]);
        }
    }

    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->cancel();
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        // Create payment record
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if ($subscription) {
            \App\Models\Payment::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'payment_gateway' => 'stripe',
                'transaction_id' => $invoice->id,
                'stripe_payment_intent_id' => $invoice->payment_intent,
                'amount' => $invoice->amount_paid / 100,
                'currency' => strtoupper($invoice->currency),
                'status' => 'completed',
                'paid_at' => now(),
            ]);
        }
    }

    protected function handleInvoicePaymentFailed($invoice)
    {
        // Handle failed payment - send notification, etc.
        $subscription = \App\Models\Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if ($subscription) {
            // Create failed payment record
            \App\Models\Payment::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'payment_gateway' => 'stripe',
                'transaction_id' => $invoice->id,
                'amount' => $invoice->amount_due / 100,
                'currency' => strtoupper($invoice->currency),
                'status' => 'failed',
            ]);

            // Send notification to user
            // $subscription->user->notify(new PaymentFailedNotification());
        }
    }
}

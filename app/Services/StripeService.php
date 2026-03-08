<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    private string $baseUrl = 'https://api.stripe.com/v1';

    public function createOrGetCustomer(User $user): array
    {
        if ($user->stripe_customer_id) {
            return ['id' => $user->stripe_customer_id];
        }

        if (!$this->isConfigured()) {
            $fakeId = 'cus_local_' . $user->id;
            $user->update(['stripe_customer_id' => $fakeId]);

            return ['id' => $fakeId, 'mode' => 'local-sandbox'];
        }

        $response = Http::asForm()
            ->withBasicAuth(config('services.stripe.secret'), '')
            ->post($this->baseUrl . '/customers', [
                'email' => $user->email,
                'name' => $user->name,
                'metadata[user_id]' => $user->id,
            ])
            ->throw()
            ->json();

        $user->update(['stripe_customer_id' => $response['id'] ?? null]);

        return $response;
    }

    public function createSubscription(User $user, string $priceId, ?string $paymentMethodId = null): array
    {
        $customer = $this->createOrGetCustomer($user);

        if (!$this->isConfigured()) {
            return [
                'id' => 'sub_local_' . $user->id . '_' . now()->format('YmdHis'),
                'customer' => $customer['id'],
                'status' => 'active',
                'price_id' => $priceId,
                'payment_method_id' => $paymentMethodId,
                'mode' => 'local-sandbox',
            ];
        }

        $payload = [
            'customer' => $customer['id'],
            'items[0][price]' => $priceId,
            'expand[0]' => 'latest_invoice.payment_intent',
        ];

        if ($paymentMethodId !== null) {
            $payload['default_payment_method'] = $paymentMethodId;
        }

        return Http::asForm()
            ->withBasicAuth(config('services.stripe.secret'), '')
            ->post($this->baseUrl . '/subscriptions', $payload)
            ->throw()
            ->json();
    }

    public function cancelSubscription(string $subscriptionId): array
    {
        if (!$this->isConfigured()) {
            return [
                'id' => $subscriptionId,
                'status' => 'canceled',
                'mode' => 'local-sandbox',
            ];
        }

        return Http::asForm()
            ->withBasicAuth(config('services.stripe.secret'), '')
            ->post($this->baseUrl . '/subscriptions/' . $subscriptionId . '/cancel')
            ->throw()
            ->json();
    }

    public function handleWebhook(string $payload, ?string $signature = null): bool
    {
        $event = json_decode($payload, true);
        if (!is_array($event) || !isset($event['type'])) {
            throw new \InvalidArgumentException('Invalid Stripe webhook payload.');
        }

        Log::info('Stripe webhook received', ['type' => $event['type'], 'has_signature' => $signature !== null]);

        $object = $event['data']['object'] ?? [];

        switch ($event['type']) {
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($object);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionCancelled($object);
                break;
        }

        return true;
    }

    private function handlePaymentSucceeded(array $invoice): void
    {
        $customerId = $invoice['customer'] ?? null;
        if ($customerId === null) {
            return;
        }

        $user = User::query()->where('stripe_customer_id', $customerId)->first();
        if ($user === null) {
            return;
        }

        $user->update([
            'subscription_status' => 'active',
            'subscription_end_date' => now()->addMonth(),
        ]);
    }

    private function handlePaymentFailed(array $invoice): void
    {
        Log::warning('Payment failed for invoice', ['invoice_id' => $invoice['id'] ?? null]);
    }

    private function handleSubscriptionCancelled(array $subscription): void
    {
        $customerId = $subscription['customer'] ?? null;
        if ($customerId === null) {
            return;
        }

        $user = User::query()->where('stripe_customer_id', $customerId)->first();
        if ($user === null) {
            return;
        }

        $user->update([
            'subscription_status' => 'cancelled',
        ]);
    }

    private function isConfigured(): bool
    {
        return !empty(config('services.stripe.secret'));
    }
}

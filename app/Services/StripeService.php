<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StripeService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.stripe.secret');
    }

    public function createCustomer($user): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->asForm()->post($this->baseUrl . '/customers', [
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => ['user_id' => $user->id],
        ]);

        return $response->json();
    }

    public function createSubscription($user, $plan): array
    {
        if (!$user->stripe_customer_id) {
            $customer = $this->createCustomer($user);
            $user->update(['stripe_customer_id' => $customer['id']]);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->asForm()->post($this->baseUrl . '/subscriptions', [
            'customer' => $user->stripe_customer_id,
            'items' => [['price' => $plan->stripe_price_id]],
        ]);

        return $response->json();
    }

    public function cancelSubscription(string $subscriptionId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->delete($this->baseUrl . '/subscriptions/' . $subscriptionId);

        return $response->json();
    }
}

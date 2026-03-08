<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    public function createOrder(float $amount, string $currency = 'USD'): array
    {
        if (!$this->isConfigured()) {
            return [
                'id' => 'paypal_local_' . now()->format('YmdHis'),
                'status' => 'CREATED',
                'amount' => $amount,
                'currency' => $currency,
                'mode' => 'local-sandbox',
            ];
        }

        $token = $this->getAccessToken();
        $baseUrl = $this->baseUrl();

        return Http::withToken($token)
            ->post($baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ]],
            ])
            ->throw()
            ->json();
    }

    private function getAccessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth((string) config('services.paypal.client_id'), (string) config('services.paypal.secret'))
            ->post($this->baseUrl() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ])
            ->throw()
            ->json();

        return (string) ($response['access_token'] ?? '');
    }

    private function baseUrl(): string
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function isConfigured(): bool
    {
        return !empty(config('services.paypal.client_id')) && !empty(config('services.paypal.secret'));
    }
}

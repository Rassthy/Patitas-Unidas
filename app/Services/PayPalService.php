<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    private function getAccessToken()
    {
        $response = Http::withBasicAuth(
            config('services.paypal.client_id'),
            config('services.paypal.secret')
        )->asForm()->post(config('services.paypal.base_url') . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials'
        ]);

        return $response->json()['access_token'];
    }

    public function createOrder($amount)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post(config('services.paypal.base_url') . '/v2/checkout/orders', [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => $amount
                    ]
                ]]
            ]);

        return $response->json();
    }

    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post(
                config('services.paypal.base_url') . "/v2/checkout/orders/$orderId/capture",
                new \stdClass()
            );

        return $response->json();
    }
}
<?php

namespace App\Services\PaymentProvider;

use Illuminate\Support\Facades\Http;

class PaymentProviderService
{
    /**
     * Call api of payment service provider and return checkout url
     * 
     * @param array $payload
     * @return string
     */
    public static function getCheckoutUrl($payload)
    {
        $url = \config('paymentprovider.url') . "/api/checkout";
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $payload);
        
        $checkoutUrl = '';

        if ($response->successful())
        {
            $checkoutUrl = $response->json()['checkout_url'];
        }
        
        return $checkoutUrl;
    }
}
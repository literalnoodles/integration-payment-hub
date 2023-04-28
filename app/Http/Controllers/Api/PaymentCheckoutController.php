<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentCheckoutRequest;
use App\Services\Merchant\MerchantServiceFactory;
use Illuminate\Http\Request;

class PaymentCheckoutController extends Controller
{
    /**
     * Receive and handle checkout requests from e-Commerce platforms
     * 
     * @param string $platform
     * @return \Illuminate\Http\Response
     */
    public function checkout(PaymentCheckoutRequest $request, $platform)
    {
        // getting merchant service
        $merchantService = MerchantServiceFactory::create($platform);
        
        // getting payload
        $payload = $request->all();

        // verify the checkout request
        if (!$merchantService->verifyCheckoutRequest($payload))
        {
            return response()->json([
                'error' => 'Invalid request!'
            ], 400);
        };

        // save requests to the database
        $paymentRecord = $merchantService->saveRequest($payload);

        // get checkout_url
        $checkoutUrl = $merchantService->getAndUpdateCheckoutUrl($payload, $paymentRecord);
        
        if (!$checkoutUrl)
        {
            return response()->json([
                'error' => 'Cannot get checkout_url'
            ], 400);
        }

        return response()->json([
            'checkout_url' => $checkoutUrl
        ]);
    }
}

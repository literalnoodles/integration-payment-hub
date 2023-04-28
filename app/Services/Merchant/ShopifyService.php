<?php

namespace App\Services\Merchant;

use App\Models\PaymentRequest;
use App\Services\PaymentProvider\PaymentProviderService;

class ShopifyService extends AbstractMerchantService
{
    /**
     * Create a new instance
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return rule for validating merchant checkout payment request
     * 
     * @return array
     */
    public function getRuleCheckoutPaymentRequest()
    {
        return [
            "x_checkout_id" => ["required", "string"],
            "x_shop_id" => ["required", "string"],
            "x_amount" => ["required", "numeric"],
            "x_currency" => ["required", "string"],
            "x_customer_email" => ["required", "email"],
            "x_url_callback" => ["required", "string"],
            "x_signature" => ["required", "string"],
        ];
    }

    /**
     * Verify checkout request
     * 
     * @param array $payload
     * @return bool
     */
    public function verifyCheckoutRequest($payload)
    {
        $signature = "x_checkout_id={$payload['x_checkout_id']}"
            . "x_shop_id={$payload['x_shop_id']}"
            . "x_amount={$payload['x_amount']}"
            . "x_currency={$payload['x_currency']}"
            . \config('merchant.shopify.key');
        $hashedSignature = hash('sha256', $signature);
        return $payload['x_signature'] === $hashedSignature;
    }

    /**
     * Save request to database
     * 
     * @param array $payload
     * @return \App\Models\PaymentRequest
     */
    public function saveRequest($payload)
    {
        // check for exist record in database
        $paymentRecord = PaymentRequest::where('checkout_id', $payload['x_checkout_id'])
            ->where('platform', 'shopify')
            ->first();

        // if the request is already saved in the table -> return it immediately
        if ($paymentRecord)
        {
            return $paymentRecord;
        }

        // else save it and return
        return PaymentRequest::create([
            'checkout_id' => $payload['x_checkout_id'],
            'store_id' => $payload['x_shop_id'],
            'amount' => $payload['x_amount'],
            'currency' => $payload['x_currency'],
            'customer_email' => $payload['x_customer_email'],
            'callback_url' => $payload['x_url_callback'],
            'platform' => 'shopify'
        ]);
    }

    /**
     * Prepare params for api call to get checkout_url
     * 
     * @param array $payload
     * @return array
     */
    protected function prepareParamsForCheckoutRequest($payload)
    {
        return [
            'merchant_store_id' => $payload['x_shop_id'],
            'email' => $payload['x_customer_email'],
            'amount' => $payload['x_amount'],
            'currency_code' => $payload['x_currency'],
            'merchant_order_id' => $payload['x_checkout_id'],
            'callback_url' => $payload['x_url_callback'],
        ];
    }
}

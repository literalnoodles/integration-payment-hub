<?php

namespace App\Services\Merchant;

use App\Models\PaymentRequest;
use App\Services\PaymentProvider\PaymentProviderService;

class Cafe24Service extends AbstractMerchantService
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
            "cafe24_checkout_id" => ["required", "string"],
            "cafe24_store_id" => ["required", "string"],
            "cafe24_amount" => ["required", "numeric"],
            "cafe24_currency_code" => ["required", "string"],
            "cafe24_customer_email" => ["required", "email"],
            "cafe24_payment_callback_url" => ["required", "string"],
            "cafe24_hash_data" => ["required", "string"],
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
        $signature = $payload['cafe24_amount'] . $payload['cafe24_currency_code']
            . $payload['cafe24_checkout_id'] . $payload['cafe24_store_id'];

        $hashedSignature = base64_encode(hash_hmac(
            'sha256', $signature, \config('merchant.cafe24.key'), true
        ));

        return $payload['cafe24_hash_data'] === $hashedSignature;
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
        $paymentRecord = PaymentRequest::where('checkout_id', $payload['cafe24_checkout_id'])
            ->where('platform', 'cafe24')
            ->first();

        // if the request is already saved in the table -> return it immediately
        if ($paymentRecord)
        {
            return $paymentRecord;
        }

        // else save it and return
        return PaymentRequest::create([
            'checkout_id' => $payload['cafe24_checkout_id'],
            'store_id' => $payload['cafe24_store_id'],
            'amount' => $payload['cafe24_amount'],
            'currency' => $payload['cafe24_currency_code'],
            'customer_email' => $payload['cafe24_customer_email'],
            'callback_url' => $payload['cafe24_payment_callback_url'],
            'platform' => 'cafe24'
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
            'merchant_store_id' => $payload['cafe24_store_id'],
            'email' => $payload['cafe24_customer_email'],
            'amount' => $payload['cafe24_amount'],
            'currency_code' => $payload['cafe24_currency_code'],
            'merchant_order_id' => $payload['cafe24_checkout_id'],
            'callback_url' => $payload['cafe24_payment_callback_url'],
        ];
    }
}
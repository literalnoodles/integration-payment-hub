<?php

namespace App\Services\Merchant;

use App\Models\PaymentRequest;
use App\Services\PaymentProvider\PaymentProviderService;

abstract class AbstractMerchantService
{
    /**
     * Create a new instance
     */
    public function __construct()
    {
    }

    /**
     * Return rule for validating merchant checkout payment request
     */
    public function getRuleCheckoutPaymentRequest()
    {
    }

    /**
     * Verify checkout request
     * 
     * @param array $payload
     * @return bool
     */
    public function verifyCheckoutRequest($payload)
    {
    }

    /**
     * Save request to database
     * 
     * @param array $payload
     * @return \App\Models\PaymentRequest
     */
    public function saveRequest($payload)
    {
    }

    /**
     * Prepare params for api call to get checkout_url
     * 
     * @param array $payload
     * @return array
     */
    protected function prepareParamsForCheckoutRequest($payload)
    {
    }

    /**
     * Prepare parameters, call Payment Service Provider API to get checkout url
     * 
     * @param array $payload
     * @param \App\Models\PaymentRequest $paymentRecord
     * @return string
     */
    public function getAndUpdateCheckoutUrl($payload, $paymentRecord)
    {
        // if record already has checkout_url -> simply return
        if ($paymentRecord->checkout_url)
        {
            return $paymentRecord->checkout_url;
        }

        // prepare params to call api
        $params = $this->prepareParamsForCheckoutRequest($payload);
        $checkoutUrl = PaymentProviderService::getCheckoutUrl($params);

        // update checkout url
        if ($checkoutUrl)
        {
            $paymentRecord->checkout_url = $checkoutUrl;
            $paymentRecord->save();
        }

        return $checkoutUrl;
    }
}
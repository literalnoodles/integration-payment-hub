<?php

namespace App\Http\Requests;

use App\Services\Merchant\MerchantServiceFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // make sure platform is in our allow list of merchants
        $rule = [
            'platform' => [
                'required',
                Rule::in(MerchantServiceFactory::allowedMerchants)
            ]
        ];

        // get merchant service
        $merchantService = MerchantServiceFactory::create($this->route('platform'));

        // adding validation rule
        if ($merchantService)
        {
            $rule = array_merge($rule, $merchantService->getRuleCheckoutPaymentRequest());
        }

        return $rule;
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation()
    {
        $this->merge(['platform' => $this->route('platform')]);
    }
}

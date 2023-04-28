<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentProviderController extends Controller
{
    /**
     * fake API to return checkout url
     */
    public function checkout()
    {
        return response()->json([
            'checkout_url' => "http://localhost:3000/api/widget/200"
        ]);
    }
}

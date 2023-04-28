<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'namespace' => 'App\Http\Controllers\Api',
], function() {
    // the main api
    Route::post('payment-checkout/{platform}', 'PaymentCheckoutController@checkout')->name('checkout-request');

    // fake api for payment provider
    Route::post('checkout', 'PaymentProviderController@checkout')->name('checkout');
});
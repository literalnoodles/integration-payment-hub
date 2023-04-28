## Installation
### Prerequisite
- PHP (tested using PHP 7.4.30)
- Composer installed
- MySQL
### Setup guide
- Install dependency
```console
composer install
```
- Create .env file

Clone the file `.env.example` and rename it as `.env`

Remember to update database information according to your MySQL setup.

- Create a database name `integration_payment_hub`

- Running migration
```console
php artisan migrate
```

- Start the main server (responsible for `Payment Integration Hub`)
```console
php artisan serve --port 8000
```

- Start the fake api server (responsible for `Payment Service Provider's Checkout API`)
```console
php artisan serve --port 3000
```

- Import test requests to Postman

The requests collection is save in the file `integration-payment-hub.postman_collection.json`

#
## Explain
Sending the POST request to http://localhost:8000/api/payment-checkout/cafe24 or http://localhost:8000/api/payment-checkout/shopify (the requests are already provided in the POSTMAN json file above)

**Require 1**: Receive Checkout requests from the e-Commerce platforms: Shopify and Cafe24.

For future scenarios, simply implement a new service for the new platform like `ShopifyService` class or `Cafe24Service` class extending `AbstractMerchantService` and add it to Factory class

**Require 2**: Validate Checkout request.

The request body is then is being validated in `PaymentCheckoutRequest`

The signature is being verified in method `verifyCheckoutRequest`

**Require 3**: Save requests to the database.

The request is then saved to `payment_requests` table using `saveRequest` method

**Require 4**: Prepare parameters for Checkout API call (for step 3).

Implemented in `prepareParamsForCheckoutRequest` method

**Require 5**: Send request to Checkout API to get Checkout URL (step 3).

Implemented in `getCheckoutUrl` method of `PaymentProviderService` class

This service will call a fake api and check for checkout_url in the response

**Require 6**: Return Checkout URL to platform

Return the checkout_url in json format

**Require 7**: Prevent duplicate Checkout requests:

Implemented in `getAndUpdateCheckoutUrl` method.

It's going to check for existing `checkout_url` in the record. If it's already existed, the request is already finished before, simply return the result back to user.

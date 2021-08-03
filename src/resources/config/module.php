<?php

declare(strict_types=1);

use Vanilo\Adyen\AdyenPaymentGateway;

return [
    'gateway' => [
        'register' => true,
        'id' => AdyenPaymentGateway::DEFAULT_ID
    ],
    'bind' => true,
    'view' => null, // null equals the default view for rendering
    'is_test' => (bool) env('ADYEN_IS_TEST', false),
    'api_key' => env('ADYEN_API_KEY'),
    'merchant_account' => env('ADYEN_MERCHANT_ACCOUNT'),
    'client_key' => env('ADYEN_CLIENT_KEY'),
    'live_endpoint_url_prefix' => env('ADYEN_LIVE_ENDPOINT_URL_PREFIX')
];

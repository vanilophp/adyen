<?php

declare(strict_types=1);

use Vanilo\Adyen\AdyenPaymentGateway;

return [
    'gateway' => [
        'register' => true,
        'id' => AdyenPaymentGateway::DEFAULT_ID
    ],
    'bind' => true,
    'xxx' => env('ADYEN_XXX'),
];

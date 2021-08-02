<?php

declare(strict_types=1);

namespace Vanilo\Adyen;

use Illuminate\Http\Request;
use Vanilo\Adyen\Client\AdyenClient;
use Vanilo\Adyen\Messages\AdyenPaymentRequest;
use Vanilo\Contracts\Address;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;

class AdyenPaymentGateway implements PaymentGateway
{
    public const DEFAULT_ID = 'adyen';

    private AdyenClient $adyenClient;

    public function __construct(AdyenClient $adyenClient)
    {
        $this->adyenClient = $adyenClient;
    }

    public static function getName(): string
    {
        return 'Adyen';
    }

    public function createPaymentRequest(Payment $payment, Address $shippingAddress = null, array $options = []): PaymentRequest
    {
        return new AdyenPaymentRequest(
            $this->adyenClient->getClientKey(),
            $this->adyenClient->getEnvironment(),
        );
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        // @todo implement
    }

    public function isOffline(): bool
    {
        return false;
    }
}

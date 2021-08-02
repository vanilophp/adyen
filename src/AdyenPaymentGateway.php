<?php

declare(strict_types=1);

namespace Vanilo\Adyen;

use Illuminate\Http\Request;
use Vanilo\Adyen\Contracts\AdyenClient;
use Vanilo\Adyen\Factories\RequestFactory;
use Vanilo\Contracts\Address;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;

class AdyenPaymentGateway implements PaymentGateway
{
    public const DEFAULT_ID = 'adyen';

    private AdyenClient $adyenClient;

    private ?RequestFactory $requestFactory = null;

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
        if (null === $this->requestFactory) {
            $this->requestFactory = new RequestFactory($this->adyenClient);
        }

        return $this->requestFactory->create($payment, $options);
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

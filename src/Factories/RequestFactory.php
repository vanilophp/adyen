<?php

declare(strict_types=1);

/**
 * Contains the RequestFactory class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Factories;

use Vanilo\Adyen\Contracts\AdyenClient;
use Vanilo\Adyen\Messages\AdyenPaymentRequest;
use Vanilo\Payment\Contracts\Payment;

final class RequestFactory
{
    private AdyenClient $adyenClient;

    public function __construct(AdyenClient $adyenClient)
    {
        $this->adyenClient = $adyenClient;
    }

    public function create(Payment $payment, array $options = []): AdyenPaymentRequest
    {
        $locale = $options['locale'] ?? null;
        $paymentMethods = $this->adyenClient->getPaymentMethods($payment, $locale);

        return new AdyenPaymentRequest(
            $payment,
            $paymentMethods,
            $this->adyenClient->getClientKey(),
            $this->adyenClient->getEnvironment()
        );
    }
}

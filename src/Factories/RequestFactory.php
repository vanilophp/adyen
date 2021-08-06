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
use Vanilo\Payment\Support\ReplacesPaymentUrlParameters;

final class RequestFactory
{
    use ReplacesPaymentUrlParameters;

    private const URL_KEYS = ['submit', 'details'];

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
            $this->adyenClient->getEnvironment(),
            $options['locale'] ?? app()->getLocale(),
            $this->processUrls($options['urls'] ?? [], $payment)
        );
    }

    private function processUrls($urls, Payment $payment): array
    {
        if (!is_array($urls) || empty($urls)) {
            return [];
        }

        $result = [];
        foreach (self::URL_KEYS as $urlKey) {
            $url = $urls[$urlKey] ?? false;
            if (is_string($url)) {
                $result[$urlKey] = $this->replaceUrlParameters($url, $payment);
            }
        }

        return $result;
    }
}

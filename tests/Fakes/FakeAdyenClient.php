<?php

declare(strict_types=1);

/**
 * Contains the FakeAdyenClient class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Tests\Fakes;

use Adyen\Environment;
use Vanilo\Adyen\Contracts\AdyenClient;
use Vanilo\Adyen\Messages\AdyenCreatePaymentResponse;
use Vanilo\Adyen\Models\AdyenPaymentResult;
use Vanilo\Adyen\Notifications\NotificationRequestItem;
use Vanilo\Payment\Contracts\Payment;

class FakeAdyenClient implements AdyenClient
{
    private ?string $clientKey = null;

    private ?string $environment = null;

    private ?string $apiKey = null;

    private ?string $merchantAccount = null;

    private ?array $adyenCreatePaymentRequestData = null;

    public function getClientKey(): string
    {
        return $this->clientKey ?? config('vanilo.adyen.client_key') ?? '';
    }

    public function getEnvironment(): string
    {
        return $this->environment ?? (config('vanilo.adyen.is_test') ? Environment::TEST : Environment::LIVE);
    }

    public function getApiKey(): string
    {
        return $this->apiKey ?? config('vanilo.adyen.api_key') ?? '';
    }

    public function getMerchantAccount(): string
    {
        return $this->merchantAccount ?? config('vanilo.adyen.merchant_account') ?? '';
    }

    public function getPaymentMethods(Payment $payment, string $locale = null): array
    {
        return [
            "paymentMethods" => [
                [
                    "brands" => ["mc", "visa", "amex", "mealVoucher_FR" ],
                    "details" => [
                        ["key" => "encryptedCardNumber", "type" => "cardToken"],
                        ["key" => "encryptedSecurityCode", "type" => "cardToken"],
                        ["key" => "encryptedExpiryMonth", "type" => "cardToken"],
                        ["key" => "encryptedExpiryYear", "type" => "cardToken"],
                        ["key" => "holderName", "optional" => true,  "type" => "text"],
                    ],
                    "name" => "Credit Card",
                    "type" => "scheme",
                ],
                [
                    "name" => "Paysafecard",
                    "type" => "paysafecard",
                ],
                [
                    "name" => "Trustly",
                    "type" => "trustly",
                ]
            ]
        ];
    }

    public function submitPayment(Payment $payment, $stateDataPaymentMethod, string $returnUrl): AdyenCreatePaymentResponse
    {
        $this->adyenCreatePaymentRequestData = [
            "paymentMethod" => $stateDataPaymentMethod,
            "amount" => [
                "currency" => $payment->getCurrency(),
                "value" => intval($payment->getAmount() * 100) // @todo, some currencies might not be in "cents"
            ],
            "reference" => $payment->getPaymentId(),
            "returnUrl" => $returnUrl,
            "merchantAccount" => $this->getMerchantAccount(),
        ];

        return new AdyenCreatePaymentResponse(AdyenPaymentResult::AUTHORISED, null);
    }

    public function getAdyenCreatePaymentRequestData(): ?array
    {
        return $this->adyenCreatePaymentRequestData;
    }

    public function setClientKey(?string $clientKey): void
    {
        $this->clientKey = $clientKey;
    }

    public function setEnvironment(?string $environment): void
    {
        $this->environment = $environment;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function setMerchantAccount(?string $merchantAccount): void
    {
        $this->merchantAccount = $merchantAccount;
    }

    public function verifyHMAC(NotificationRequestItem $notification): bool
    {
        return true;
    }
}

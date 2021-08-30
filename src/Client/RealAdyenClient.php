<?php

declare(strict_types=1);

/**
 * Contains the AdyenClient class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-07-30
 *
 */

namespace Vanilo\Adyen\Client;

use Adyen\Client as NativeAdyenClient;
use Adyen\Environment;
use Adyen\Service\Checkout;
use Adyen\Util\HmacSignature;
use Vanilo\Adyen\Contracts\AdyenClient;
use Vanilo\Adyen\Messages\AdyenCreatePaymentResponse;
use Vanilo\Adyen\Notifications\NotificationRequestItem;
use Vanilo\Payment\Contracts\Payment;

class RealAdyenClient implements AdyenClient
{
    private NativeAdyenClient $nativeClient;

    private string $clientKey;

    private ?Checkout $checkoutSvc = null;
    private string $hmacKey;

    public function __construct(
        string $apiKey,
        string $merchantAccount,
        string $clientKey,
        string $hmacKey,
        string $liveEndpointUrlPrefix = null,
        bool $isTestEnvironment = false
    ) {
        $this->clientKey = $clientKey;
        $this->hmacKey = $hmacKey;

        $this->nativeClient = new NativeAdyenClient();
        $this->nativeClient->setXApiKey($apiKey);
        $this->nativeClient->setMerchantAccount($merchantAccount);
        $this->nativeClient->setEnvironment(
            $isTestEnvironment ? Environment::TEST : Environment::LIVE,
            $liveEndpointUrlPrefix
        );
    }

    public function getNativeClient(): NativeAdyenClient
    {
        return $this->nativeClient;
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function getEnvironment(): string
    {
        return $this->nativeClient->getConfig()->getEnvironment();
    }

    public function getApiKey(): string
    {
        return $this->nativeClient->getConfig()->getXApiKey();
    }

    public function getMerchantAccount(): string
    {
        return $this->nativeClient->getConfig()->getMerchantAccount();
    }

    public function getPaymentMethods(Payment $payment, string $locale = null): array
    {
        return $this->adyenCheckoutService()->paymentMethods([
            'countryCode' => $payment->getPayable()->getBillpayer()->getBillingAddress()->getCountryCode(),
            'shopperLocale' => $locale ?? app()->getLocale(),
            'amount' => [
                'currency' => $payment->getCurrency(),
                'value' => intval($payment->getAmount() * 100) // @todo, some currencies might not be in "cents"
            ],
            'channel' => 'Web',
            'merchantAccount' => $this->nativeClient->getConfig()->getMerchantAccount()
        ]);
    }

    public function verifyHMAC(NotificationRequestItem $notification): bool
    {
        $hmac = new HmacSignature();
        if (!$hmac->isHmacSupportedEventCode($notification->toArray())) {
            return true;
        }

        return $hmac->isValidNotificationHMAC($this->hmacKey, $notification->toArray());
    }

    public function submitPayment(Payment $payment, $stateDataPaymentMethod, string $returnUrl): AdyenCreatePaymentResponse
    {
        $response = $this->adyenCheckoutService()->payments([
            "paymentMethod" => $stateDataPaymentMethod,
            "amount" => [
                "currency" => $payment->getCurrency(),
                "value" => intval($payment->getAmount() * 100) // @todo, some currencies might not be in "cents"
            ],
            "reference" => $payment->getPaymentId(),
            "returnUrl" => $returnUrl,
            "merchantAccount" => $this->getMerchantAccount(),
        ]);

        return new AdyenCreatePaymentResponse($response['resultCode'], $response['action'] ?? null);
    }

    private function adyenCheckoutService(): Checkout
    {
        if (null === $this->checkoutSvc) {
            $this->checkoutSvc = new Checkout($this->nativeClient);
        }

        return $this->checkoutSvc;
    }
}

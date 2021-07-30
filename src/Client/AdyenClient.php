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

class AdyenClient
{
    private NativeAdyenClient $nativeClient;

    private string $clientKey;

    public function __construct(
        string $apiKey,
        string $merchantAccount,
        string $clientKey,
        string $liveEndpointUrlPrefix = null,
        bool $isTestEnvironment = false
    ) {
        $this->nativeClient = new NativeAdyenClient();
        $this->nativeClient->setXApiKey($apiKey);
        $this->nativeClient->setMerchantAccount($merchantAccount);
        $this->nativeClient->setEnvironment(
            $isTestEnvironment ? Environment::TEST : Environment::LIVE,
            $liveEndpointUrlPrefix
        );

        $this->clientKey = $clientKey;
    }

    public function getNativeClient(): NativeAdyenClient
    {
        return $this->nativeClient;
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }
}

<?php

declare(strict_types=1);

/**
 * Contains the ResponseFactory class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-03
 *
 */

namespace Vanilo\Adyen\Factories;

use Illuminate\Http\Request;
use Vanilo\Adyen\Contracts\AdyenClient;
use Vanilo\Adyen\Exceptions\HMACVerificationFailedException;
use Vanilo\Adyen\Messages\AdyenPaymentResponse;
use Vanilo\Adyen\Notifications\WebhookNotification;

final class ResponseFactory
{
    private AdyenClient $adyenClient;

    public function __construct(AdyenClient $adyenClient)
    {
        $this->adyenClient = $adyenClient;
    }

    public function createFromRequest(Request $request): AdyenPaymentResponse
    {
        $notification = WebhookNotification::createFromRequest($request);
        $event = $notification->item();
        if (!$this->adyenClient->verifyHMAC($event)) {
            throw HMACVerificationFailedException::fromNotification($event);
        }

        return new AdyenPaymentResponse(
            $event->merchantReference(),
            $event->event(),
            null,
            $event->amount(),
            $event->additionalData()->hmacSignature()
        );
    }
}

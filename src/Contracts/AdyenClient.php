<?php

declare(strict_types=1);

/**
 * Contains the AdyenClient interface.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Contracts;

use Vanilo\Adyen\Messages\AdyenCreatePaymentResponse;
use Vanilo\Adyen\Notifications\NotificationRequestItem;
use Vanilo\Payment\Contracts\Payment;

interface AdyenClient
{
    public function getClientKey(): string;

    public function getEnvironment(): string;

    public function getApiKey(): string;

    public function getMerchantAccount(): string;

    public function getPaymentMethods(Payment $payment, string $locale = null): array;

    public function submitPayment(Payment $payment, $stateDataPaymentMethod, string $returnUrl): AdyenCreatePaymentResponse;

    public function verifyHMAC(NotificationRequestItem $notification): bool;
}

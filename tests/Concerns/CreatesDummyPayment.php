<?php

declare(strict_types=1);

/**
 * Contains the CreatesDummyPayment trait.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Tests\Concerns;

use Vanilo\Adyen\Tests\Dummies\Order as DummyOrder;
use Vanilo\Payment\Models\Payment;
use Vanilo\Payment\Models\PaymentMethod;

trait CreatesDummyPayment
{
    private function createDummyPayment(string $currency = 'EUR', float $amount = 5, string $paymentMethodId = null): Payment
    {
        return Payment::create([
            'currency' => $currency,
            'amount' => $amount,
            'payable_type' => DummyOrder::class,
            'payable_id' => 1,
            'payment_method_id' => $paymentMethodId ?? PaymentMethod::create(['name' => 'Adyen', 'gateway' => 'adyen']),
        ]);
    }
}

<?php

declare(strict_types=1);

/**
 * Contains the CreatesCompleteDummyPayment trait.
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

trait CreatesCompleteDummyPayment
{
    private function createCompleteDummyPayment(string $currency = 'EUR', float $amount = 5): Payment
    {
        $order = DummyOrder::create([
            'currency' => $currency,
            'amount' => $amount,
        ]);
        return Payment::create([
            'currency' => $currency,
            'amount' => $amount,
            'payable_type' => DummyOrder::class,
            'payable_id' => $order->id,
            'payment_method_id' => $paymentMethodId ?? PaymentMethod::create(['name' => 'Adyen', 'gateway' => 'adyen']),
        ]);
    }
}
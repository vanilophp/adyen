<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Tests\Gateway;

use Vanilo\Adyen\AdyenPaymentGateway;
use Vanilo\Adyen\Tests\MakesDummyAdyenConfiguration;
use Vanilo\Adyen\Tests\TestCase;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\PaymentGateways;

class RegistrationTest extends TestCase
{
    use MakesDummyAdyenConfiguration;

    /** @test */
    public function the_gateway_is_registered_out_of_the_box_with_defaults()
    {
        $this->assertCount(2, PaymentGateways::ids());
        $this->assertContains(AdyenPaymentGateway::DEFAULT_ID, PaymentGateways::ids());
    }

    /** @test */
    public function the_gateway_can_be_instantiated()
    {
        $gateway = PaymentGateways::make('adyen');

        $this->assertInstanceOf(PaymentGateway::class, $gateway);
        $this->assertInstanceOf(AdyenPaymentGateway::class, $gateway);
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $this->setDummyAdyenConfiguration();
    }
}

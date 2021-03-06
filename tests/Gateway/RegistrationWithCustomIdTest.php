<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Tests\Gateway;

use Vanilo\Adyen\AdyenPaymentGateway;
use Vanilo\Adyen\Tests\Concerns\MakesDummyAdyenConfiguration;
use Vanilo\Adyen\Tests\TestCase;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\PaymentGateways;

class RegistrationWithCustomIdTest extends TestCase
{
    use MakesDummyAdyenConfiguration;

    protected function setUp(): void
    {
        PaymentGateways::reset();
        parent::setUp();
    }

    /** @test */
    public function the_gateway_id_can_be_changed_from_within_the_configuration()
    {
        $this->assertCount(2, PaymentGateways::ids());
        $this->assertContains('alternative_gw_name', PaymentGateways::ids());
    }

    /** @test */
    public function the_gateway_can_be_instantiated()
    {
        $payPalGateway = PaymentGateways::make('alternative_gw_name');

        $this->assertInstanceOf(PaymentGateway::class, $payPalGateway);
        $this->assertInstanceOf(AdyenPaymentGateway::class, $payPalGateway);
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $this->setDummyAdyenConfiguration();
        config(['vanilo.adyen.gateway.id' => 'alternative_gw_name']);
    }
}

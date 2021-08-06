<?php

declare(strict_types=1);

/**
 * Contains the ProviderTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-07-30
 *
 */

namespace Vanilo\Adyen\Tests;

use Adyen\Environment;
use ReflectionClass;
use ReflectionProperty;
use Vanilo\Adyen\AdyenPaymentGateway;
use Vanilo\Adyen\Client\RealAdyenClient;
use Vanilo\Adyen\Tests\Concerns\MakesDummyAdyenConfiguration;
use Vanilo\Payment\PaymentGateways;

class ProviderTest extends TestCase
{
    use MakesDummyAdyenConfiguration;

    /** @test */
    public function it_configures_the_di_registered_adyen_client()
    {
        $client = app(RealAdyenClient::class);

        $this->assertInstanceOf(RealAdyenClient::class, $client);

        $this->assertEquals('m8Hg*UbaEX5', $client->getApiKey());
        $this->assertEquals('VaniloECOM', $client->getMerchantAccount());
        $this->assertEquals(Environment::TEST, $client->getEnvironment());

        $this->assertEquals('test_ClientKEY_444', $client->getClientKey());
    }

    /** @test */
    public function it_configures_the_registered_adyen_gateway()
    {
        config(['vanilo.adyen.urls' => [
            'submit' => 'http://url.to/submit',
            'details' => 'http://url.to/details',
            'return' => 'http://url.to/return',
        ]]);
        $gateway = PaymentGateways::make('adyen');
        $this->assertInstanceOf(AdyenPaymentGateway::class, $gateway);

        $submitUrl = $this->getPrivateProperty(AdyenPaymentGateway::class, 'submitUrl');
        $this->assertEquals('http://url.to/submit', $submitUrl->getValue($gateway));

        $detailsUrl = $this->getPrivateProperty(AdyenPaymentGateway::class, 'detailsUrl');
        $this->assertEquals('http://url.to/details', $detailsUrl->getValue($gateway));

        $returnUrl = $this->getPrivateProperty(AdyenPaymentGateway::class, 'returnUrl');
        $this->assertEquals('http://url.to/return', $returnUrl->getValue($gateway));
    }

    /** @test */
    public function it_injects_a_real_adyen_client_into_the_gateway()
    {
        $gateway = PaymentGateways::make('adyen');
        $this->assertInstanceOf(AdyenPaymentGateway::class, $gateway);

        $adyenClient = $this->getPrivateProperty(AdyenPaymentGateway::class, 'adyenClient');
        $this->assertInstanceOf(RealAdyenClient::class, $adyenClient->getValue($gateway));
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
        $this->setDummyAdyenConfiguration();
    }

    private function getPrivateProperty(string $className, string $propertyName): ReflectionProperty
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}

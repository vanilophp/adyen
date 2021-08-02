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
use Vanilo\Adyen\Client\RealAdyenClient;
use Vanilo\Adyen\Tests\Concerns\MakesDummyAdyenConfiguration;

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

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
        $this->setDummyAdyenConfiguration();
    }
}

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
use Vanilo\Adyen\Client\AdyenClient;

class ProviderTest extends TestCase
{
    use MakesDummyAdyenConfiguration;

    /** @test */
    public function it_configures_the_di_registered_adyen_client()
    {
        $client = app(AdyenClient::class);

        $this->assertInstanceOf(AdyenClient::class, $client);

        $config = $client->getNativeClient()->getConfig();
        $this->assertEquals('m8Hg*UbaEX5', $config->getXApiKey());
        $this->assertEquals('VaniloECOM', $config->getMerchantAccount());
        $this->assertEquals(Environment::TEST, $config->getEnvironment());

        $this->assertEquals('test_ClientKEY_444', $client->getClientKey());
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
        $this->setDummyAdyenConfiguration();
    }
}

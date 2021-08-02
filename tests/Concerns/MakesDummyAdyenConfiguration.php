<?php

declare(strict_types=1);

/**
 * Contains the MakesDummyAdyenConfiguration trait.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-07-31
 *
 */

namespace Vanilo\Adyen\Tests\Concerns;

trait MakesDummyAdyenConfiguration
{
    protected function setDummyAdyenConfiguration()
    {
        config([
            'vanilo.adyen.api_key' => 'm8Hg*UbaEX5',
            'vanilo.adyen.merchant_account' => 'VaniloECOM',
            'vanilo.adyen.client_key' => 'test_ClientKEY_444',
            'vanilo.adyen.is_test' => true,
        ]);
    }
}

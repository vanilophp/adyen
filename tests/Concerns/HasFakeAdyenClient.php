<?php

declare(strict_types=1);

/**
 * Contains the HasFakeAdyenClient trait.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Tests\Concerns;

use Vanilo\Adyen\Tests\Fakes\FakeAdyenClient;

trait HasFakeAdyenClient
{
    private ?FakeAdyenClient $fakeAdyenClient = null;

    protected function fakeAdyenClient(): FakeAdyenClient
    {
        if (null === $this->fakeAdyenClient) {
            $this->fakeAdyenClient = new FakeAdyenClient();
        }

        return $this->fakeAdyenClient;
    }
}
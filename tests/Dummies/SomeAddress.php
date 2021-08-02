<?php

declare(strict_types=1);

/**
 * Contains the SomeAddress class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Tests\Dummies;

use Vanilo\Contracts\Address;

class SomeAddress implements Address
{
    private string $name;

    private string $country;

    public function __construct(string $name, string $country = 'DK')
    {
        $this->name = $name;
        $this->country = $country;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountryCode(): string
    {
        return $this->country;
    }

    public function getProvinceCode(): ?string
    {
        return '85';
    }

    public function getPostalCode(): ?string
    {
        return '4874';
    }

    public function getCity(): ?string
    {
        return 'Gedser';
    }

    public function getAddress(): string
    {
        return '23 Strandvej';
    }
}

<?php

declare(strict_types=1);

/**
 * Contains the Customer class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Tests\Dummies;

use Vanilo\Contracts\Address;
use Vanilo\Contracts\Billpayer;

class Customer implements Billpayer
{
    private string $email;

    private string $firstname;

    private string $lastname;

    private string $country;

    public function __construct(
        string $email = 'someone@example.org',
        string $firstname = 'Fritz',
        string $lastname = 'Teufel',
        string $country = 'DK'
    ) {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->country = $country;
    }

    public function isEuRegistered(): bool
    {
        return false;
    }

    public function getBillingAddress(): Address
    {
        return new SomeAddress($this->getFullName(), $this->country);
    }

    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return $this->getFullName();
    }

    public function isOrganization(): bool
    {
        return false;
    }

    public function isIndividual(): bool
    {
        return true;
    }

    public function getCompanyName(): ?string
    {
        return null;
    }

    public function getTaxNumber(): ?string
    {
        return null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function getLastName(): ?string
    {
        return $this->lastname;
    }
}

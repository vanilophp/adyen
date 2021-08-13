<?php

declare(strict_types=1);

/**
 * Contains the AdditionalData class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-13
 *
 */

namespace Vanilo\Adyen\Notifications;

use Illuminate\Support\Arr;

/**
 * @see https://docs.adyen.com/development-resources/webhooks/additional-settings
 */
final class AdditionalData
{
    private array $rawData;

    public function __construct(array $payload)
    {
        $this->rawData = $payload;
    }

    public function get(string $key, $default = null)
    {
        return Arr::get($this->rawData, $key, $default);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->rawData, $key);
    }

    public function hmacSignature(): ?string
    {
        return $this->get('hmacSignature');
    }

    public function hasHmacSignature(): bool
    {
        return $this->has('hmacSignature');
    }

    public function arn(): ?string
    {
        return $this->get('arn');
    }

    public function acquirerErrorCode(): ?string
    {
        return $this->get('acquirerErrorCode');
    }

    public function acquirerReference(): ?string
    {
        return $this->get('acquirerReference');
    }

    public function alias(): ?string
    {
        return $this->get('alias');
    }

    public function aliasType(): ?string
    {
        return $this->get('aliasType');
    }
}

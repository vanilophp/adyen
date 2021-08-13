<?php

declare(strict_types=1);

/**
 * Contains the UnderstandsStringBooleans trait.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-13
 *
 */

namespace Vanilo\Adyen\Notifications;

trait UnderstandsStringBooleans
{
    private static function boolify($value): bool
    {
        if (is_bool($value)) {
            return $value;
        } elseif (is_numeric($value)) {
            return !(0 == $value);
        } elseif (is_string($value)) {
            return in_array(strtolower($value), ['true', 'yes']);
        }

        return boolval($value); // ¯\_ (ツ)_/¯
    }
}
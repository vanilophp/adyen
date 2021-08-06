<?php

declare(strict_types=1);

/**
 * Contains the AdyenPaymentResult class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-06
 *
 */

namespace Vanilo\Adyen\Models;

use Konekt\Enum\Enum;

class AdyenPaymentResult extends Enum
{
    public const AUTHORISED = 'Authorised';
    public const ERROR = 'Error';
    public const PENDING = 'Pending';
    public const PRESENT_TO_SHOPPER = 'PresentToShopper';
    public const REFUSED = 'Refused';
    public const RECEIVED = 'Received';
}

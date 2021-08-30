<?php

declare(strict_types=1);

/**
 * Contains the HMACVerificationFailedException class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-30
 *
 */

namespace Vanilo\Adyen\Exceptions;

use Exception;
use Throwable;
use Vanilo\Adyen\Notifications\NotificationRequestItem;

class HMACVerificationFailedException extends Exception
{
    private NotificationRequestItem $requestItem;

    public function __construct(NotificationRequestItem $requestItem, Throwable $previous = null)
    {
        parent::__construct("HMAC verification has failed for Adyen Notification", 0, $previous);
        $this->requestItem = $requestItem;
    }


    public static function fromNotification(NotificationRequestItem $notificationRequestItem): HMACVerificationFailedException
    {
        return new static($notificationRequestItem);
    }
}

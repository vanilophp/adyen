<?php

declare(strict_types=1);

/**
 * Contains the WebhookNotification class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-13
 *
 */

namespace Vanilo\Adyen\Notifications;

use Illuminate\Http\Request;

/**
 * This class represents a notification message being sent by the Adyen
 * Notification subsystem. These messages are delivered via webhooks
 * which you need to set up manually, in your Adyen Customer Area
 *
 * @see https://docs.adyen.com/development-resources/webhooks/understand-notifications
 */
final class WebhookNotification
{
    use UnderstandsStringBooleans;

    private NotificationRequestItem $item;

    private bool $isLive;

    public function __construct(bool $isLive, NotificationRequestItem $item)
    {
        $this->isLive = $isLive;
        $this->item = $item;
    }

    /**
     * Even if the notificationItems is structurally an array in the payload,
     * the JSON and HTTP POST notifications always contain a single object
     * The SOAP XML may contain up to 6 items, but SOAP isn't supported
     *
     * @see https://docs.adyen.com/development-resources/webhooks/understand-notifications#notification-structure
     */
    public static function createFromRequest(Request $request): WebhookNotification
    {
        return new self(
            self::boolify($request->json('live', false)),
            NotificationRequestItem::createFromPayload($request->json('notificationItems', [])[0])
        );
    }

    public function item(): NotificationRequestItem
    {
        return $this->item;
    }

    public function isLive(): bool
    {
        return $this->isLive;
    }
}

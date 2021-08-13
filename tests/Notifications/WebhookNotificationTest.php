<?php

declare(strict_types=1);

/**
 * Contains the WebhookNotificationTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-13
 *
 */

namespace Vanilo\Adyen\Tests\Notifications;

use Illuminate\Http\Request;
use Vanilo\Adyen\Models\AdyenEvent;
use Vanilo\Adyen\Notifications\NotificationRequestItem;
use Vanilo\Adyen\Notifications\WebhookNotification;
use Vanilo\Adyen\Tests\TestCase;

class WebhookNotificationTest extends TestCase
{
    /** @test */
    public function it_can_be_created_using_the_factory_method()
    {
        $this->assertInstanceOf(WebhookNotification::class, WebhookNotification::createFromRequest($this->makeExampleRequest()));
    }

    /** @test */
    public function it_detects_whether_live_or_not()
    {
        $request = $this->makeExampleRequest();
        $notification = WebhookNotification::createFromRequest($request);
        $this->assertFalse($notification->isLive());
    }

    /** @test */
    public function it_reads_the_item_properly()
    {
        $request = $this->makeExampleRequest();
        $notification = WebhookNotification::createFromRequest($request);

        $item = $notification->item();
        $this->assertInstanceOf(NotificationRequestItem::class, $item);
        $this->assertEquals(AdyenEvent::AUTHORISATION, $item->event()->value());
        $this->assertTrue($item->event()->wasSuccessful());
    }

    private function makeExampleRequest(): Request
    {
        return new Request([], [], [], [], [], [], file_get_contents(dirname(__DIR__) . "/SampleData/notification_auth_example.json"));
    }
}

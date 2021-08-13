<?php

declare(strict_types=1);

/**
 * Contains the NotificationRequestItemTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-13
 *
 */

namespace Vanilo\Adyen\Tests\Notifications;

use Vanilo\Adyen\Models\AdyenEvent;
use Vanilo\Adyen\Notifications\AdditionalData;
use Vanilo\Adyen\Notifications\NotificationRequestItem;
use Vanilo\Adyen\Tests\TestCase;

class NotificationRequestItemTest extends TestCase
{
    /** @test */
    public function it_reads_the_event()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertInstanceOf(AdyenEvent::class, $item->event());
        $this->assertEquals(AdyenEvent::AUTHORISATION, $item->event()->value());
    }

    /** @test */
    public function it_reads_whether_the_event_was_successful()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertTrue($item->event()->wasSuccessful());
    }

    /** @test */
    public function it_reads_the_psp_reference()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertEquals('7914073381342284', $item->pspReference());
    }

    /** @test */
    public function it_reads_the_event_date()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertEquals('2019-06-28 18:03:50', $item->date()->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_reads_the_merchant_account()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertEquals('YOUR_MERCHANT_ACCOUNT', $item->merchantAccountCode());
    }

    /** @test */
    public function it_reads_the_merchant_reference()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertEquals('YOUR_REFERENCE', $item->merchantReference());
    }

    /** @test */
    public function it_reads_the_amount()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertEquals(11.30, $item->amount());
        $this->assertEquals('EUR', $item->currency());
    }

    /** @test */
    public function original_reference_is_null_when_the_payload_doesnt_contain_the_field()
    {
        $authItem = NotificationRequestItem::createFromPayload($this->getSamplePayload());
        $this->assertNull($authItem->originalReference());
    }

    /** @test */
    public function it_reads_the_original_reference()
    {
        $cancellationItem = NotificationRequestItem::createFromPayload($this->getSamplePayload('notification_cancellation_example'));
        $this->assertEquals('5312489439474842', $cancellationItem->originalReference());

        // The assertions below are extra, don't strictly belong to this particular test case:
        $this->assertEquals('7914073381342284', $cancellationItem->pspReference());
        $this->assertEquals(AdyenEvent::CANCELLATION, $cancellationItem->event()->value());
    }

    /** @test */
    public function reason_is_null_when_the_payload_doesnt_contain_the_field()
    {
        $authItem = NotificationRequestItem::createFromPayload($this->getSamplePayload());
        $this->assertNull($authItem->reason());
    }

    /** @test */
    public function it_reads_the_reason()
    {
        $cancellationItem = NotificationRequestItem::createFromPayload($this->getSamplePayload('notification_report_available_example'));
        $this->assertEquals('https://some.adyen.report.link/download', $cancellationItem->reason());
        $this->assertEquals(AdyenEvent::REPORT_AVAILABLE, $cancellationItem->event()->value());
    }

    /**
     * @see https://docs.adyen.com/development-resources/webhooks/understand-notifications#event-codes
     * @test
     */
    public function it_handles_future_adyen_event_codes_that_are_unknown_at_the_moment_of_implementation()
    {
        $futureEventCodeItem = NotificationRequestItem::createFromPayload(
            $this->getSamplePayload('notification_unknown_example')
        );

        $this->assertTrue($futureEventCodeItem->event()->isUnknown());
    }

    /** @test */
    public function it_has_additional_data()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload());

        $this->assertInstanceOf(AdditionalData::class, $item->additionalData());
    }

    /** @test */
    public function it_properly_reads_the_additional_data()
    {
        $item = NotificationRequestItem::createFromPayload($this->getSamplePayload('notification_additional_data_example'));
        $data = $item->additionalData();

        $this->assertEquals('022f0f2a92204d72a92d87a2ba50061f', $data->arn());
        $this->assertEquals('B181677540714335', $data->alias());
        $this->assertEquals('card', $data->aliasType());
        $this->assertEquals('+JWKfq4ynALK+FFzGgHnp1jSMQJMBJeb87dlph24sXw=', $data->hmacSignature());
        $this->assertEquals('too_busy_counting_profit', $data->acquirerErrorCode());
        $this->assertEquals('kJkoXC0SEB8iA', $data->acquirerReference());
    }

    private function getSamplePayload(string $file = 'notification_auth_example'): array
    {
        return json_decode(
            file_get_contents(dirname(__DIR__) . "/SampleData/$file.json"),
            true,
        )['notificationItems'][0];
    }
}

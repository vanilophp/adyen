<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Models;

use Konekt\Enum\Enum;

/**
 * @see https://docs.adyen.com/development-resources/webhooks/understand-notifications#event-codes
 */
class AdyenEvent extends Enum
{
    public const __DEFAULT = self::UNKNOWN;
    const UNKNOWN = null;

    // S T A N D A R D
    const AUTHORISATION = 'AUTHORISATION';
    const AUTHORISATION_ADJUSTMENT = 'AUTHORISATION_ADJUSTMENT';
    const CANCELLATION = 'CANCELLATION';
    const CANCEL_OR_REFUND = 'CANCEL_OR_REFUND';
    const CAPTURE = 'CAPTURE';
    const CAPTURE_FAILED = 'CAPTURE_FAILED';
    const ORDER_CLOSED = 'ORDER_CLOSED';
    const ORDER_OPENED = 'ORDER_OPENED';
    const REFUND = 'REFUND';
    const REFUND_FAILED = 'REFUND_FAILED';
    const REFUND_REVERSED = 'REFUND_REVERSED';
    const REFUND_WITH_DATA = 'REFUND_WITH_DATA';
    const REPORT_AVAILABLE = 'REPORT_AVAILABLE';
    const TECHNICAL_CANCEL = 'TECHNICAL_CANCEL';
    const VOID_PENDING_REFUND = 'VOID_PENDING_REFUND';

    // A D D I T I O N A L
    const AUTORESCUE = 'AUTORESCUE';
    const CANCEL_AUTORESCUE = 'CANCEL_AUTORESCUE';
    const MANUAL_REVIEW_ACCEPT = 'MANUAL_REVIEW_ACCEPT';
    const MANUAL_REVIEW_REJECT = 'MANUAL_REVIEW_REJECT';
    const OFFER_CLOSED = 'OFFER_CLOSED';
    const POSTPONED_REFUND = 'POSTPONED_REFUND';
    const RECURRING_CONTRACT = 'RECURRING_CONTRACT';

    // D I S P U T E
    const CHARGEBACK = 'CHARGEBACK';
    const CHARGEBACK_REVERSED = 'CHARGEBACK_REVERSED';
    const NOTIFICATION_OF_CHARGEBACK = 'NOTIFICATION_OF_CHARGEBACK';
    const NOTIFICATION_OF_FRAUD = 'NOTIFICATION_OF_FRAUD';
    const PREARBITRATION_LOST = 'PREARBITRATION_LOST';
    const PREARBITRATION_WON = 'PREARBITRATION_WON';
    const REQUEST_FOR_INFORMATION = 'REQUEST_FOR_INFORMATION';
    const SECOND_CHARGEBACK = 'SECOND_CHARGEBACK';

    // P A Y O U T
    const PAIDOUT_REVERSED = 'PAIDOUT_REVERSED';
    const PAYOUT_DECLINE = 'PAYOUT_DECLINE';
    const PAYOUT_EXPIRE = 'PAYOUT_EXPIRE';
    const PAYOUT_THIRDPARTY = 'PAYOUT_THIRDPARTY';

    protected static $unknownValuesFallbackToDefault = true;

    private bool $wasSuccessful = true;

    public function markAsSuccessful(): void
    {
        $this->wasSuccessful = true;
    }

    public function markAsFailed(): void
    {
        $this->wasSuccessful = false;
    }

    public function wasSuccessful(): bool
    {
        return $this->wasSuccessful;
    }

    public function hasFailed(): bool
    {
        return !$this->wasSuccessful;
    }
}

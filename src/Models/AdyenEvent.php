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
    public const UNKNOWN = null;

    // S T A N D A R D
    public const AUTHORISATION = 'AUTHORISATION';
    public const AUTHORISATION_ADJUSTMENT = 'AUTHORISATION_ADJUSTMENT';
    public const CANCELLATION = 'CANCELLATION';
    public const CANCEL_OR_REFUND = 'CANCEL_OR_REFUND';
    public const CAPTURE = 'CAPTURE';
    public const CAPTURE_FAILED = 'CAPTURE_FAILED';
    public const ORDER_CLOSED = 'ORDER_CLOSED';
    public const ORDER_OPENED = 'ORDER_OPENED';
    public const REFUND = 'REFUND';
    public const REFUND_FAILED = 'REFUND_FAILED';
    public const REFUND_REVERSED = 'REFUND_REVERSED';
    public const REFUND_WITH_DATA = 'REFUND_WITH_DATA';
    public const REPORT_AVAILABLE = 'REPORT_AVAILABLE';
    public const TECHNICAL_CANCEL = 'TECHNICAL_CANCEL';
    public const VOID_PENDING_REFUND = 'VOID_PENDING_REFUND';

    // A D D I T I O N A L
    public const AUTORESCUE = 'AUTORESCUE';
    public const CANCEL_AUTORESCUE = 'CANCEL_AUTORESCUE';
    public const MANUAL_REVIEW_ACCEPT = 'MANUAL_REVIEW_ACCEPT';
    public const MANUAL_REVIEW_REJECT = 'MANUAL_REVIEW_REJECT';
    public const OFFER_CLOSED = 'OFFER_CLOSED';
    public const POSTPONED_REFUND = 'POSTPONED_REFUND';
    public const RECURRING_CONTRACT = 'RECURRING_CONTRACT';

    // D I S P U T E
    public const CHARGEBACK = 'CHARGEBACK';
    public const CHARGEBACK_REVERSED = 'CHARGEBACK_REVERSED';
    public const NOTIFICATION_OF_CHARGEBACK = 'NOTIFICATION_OF_CHARGEBACK';
    public const NOTIFICATION_OF_FRAUD = 'NOTIFICATION_OF_FRAUD';
    public const PREARBITRATION_LOST = 'PREARBITRATION_LOST';
    public const PREARBITRATION_WON = 'PREARBITRATION_WON';
    public const REQUEST_FOR_INFORMATION = 'REQUEST_FOR_INFORMATION';
    public const SECOND_CHARGEBACK = 'SECOND_CHARGEBACK';

    // P A Y O U T
    public const PAIDOUT_REVERSED = 'PAIDOUT_REVERSED';
    public const PAYOUT_DECLINE = 'PAYOUT_DECLINE';
    public const PAYOUT_EXPIRE = 'PAYOUT_EXPIRE';
    public const PAYOUT_THIRDPARTY = 'PAYOUT_THIRDPARTY';

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

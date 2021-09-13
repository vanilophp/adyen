<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Messages;

use Konekt\Enum\Enum;
use Vanilo\Adyen\Models\AdyenEvent;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\PaymentStatus;
use Vanilo\Payment\Models\PaymentStatusProxy;

class AdyenPaymentResponse implements PaymentResponse
{
    private static array $eventsWithNegativeAmount = [
        AdyenEvent::CANCELLATION,
        AdyenEvent::REFUND,
        AdyenEvent::REFUND_WITH_DATA,
        AdyenEvent::CANCEL_OR_REFUND,
    ];

    private static array $eventsWithPositiveAmount = [
        AdyenEvent::AUTHORISATION,
        AdyenEvent::REFUND_REVERSED,
    ];

    private string $paymentId;

    private ?float $amountPaid;

    private AdyenEvent $nativeStatus;

    private ?PaymentStatus $status = null;

    private ?string $message;

    private ?string $transactionId;

    public function __construct(
        string $paymentId,
        AdyenEvent $nativeStatus,
        ?string $message,
        ?float $amountPaid = null,
        ?string $transactionId = null
    ) {
        $this->paymentId = $paymentId;
        $this->nativeStatus = $nativeStatus;
        $this->amountPaid = $this->calculateAmountPaid($amountPaid, $nativeStatus);
        $this->message = $message;
        $this->transactionId = $transactionId;
    }

    public function wasSuccessful(): bool
    {
        return $this->nativeStatus->wasSuccessful();
    }

    public function getMessage(): string
    {
        return $this->message ?? $this->nativeStatus->label();
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getAmountPaid(): ?float
    {
        return $this->amountPaid;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @see https://docs.adyen.com/development-resources/webhooks/understand-notifications#event-codes
     */
    public function getStatus(): PaymentStatus
    {
        if (null === $this->status) {
            $success = $this->nativeStatus->wasSuccessful();
            switch ($this->nativeStatus->value()) {

                case AdyenEvent::AUTHORISATION:
                    $this->status = $success ? PaymentStatusProxy::AUTHORIZED() : PaymentStatusProxy::DECLINED();
                break;

                case AdyenEvent::CAPTURE:
                    // @todo it's only a hypothesis that capture can only follow after an
                    //       authorization. This case needs to be verified. In general
                    //       it would be safer to return a "void" or the old status
                    $this->status = $success ? PaymentStatusProxy::PAID() : PaymentStatusProxy::AUTHORIZED();
                break;

                case AdyenEvent::CAPTURE_FAILED:
                    $this->status = PaymentStatusProxy::AUTHORIZED();
                break;

                case AdyenEvent::REFUND:
                    // @see https://docs.adyen.com/online-payments/refund
                    // From the Adyen Docs:
                    //    > You can only refund a payment after it has already
                    //    > been captured. Payments that have not yet been
                    //    > captured have to be cancelled instead.
                    $this->status = $success ? PaymentStatusProxy::REFUNDED() : PaymentStatusProxy::PAID();
                break;

                case AdyenEvent::CANCELLATION:
                    // @see https://docs.adyen.com/online-payments/cancel
                    $this->status = $success ? PaymentStatusProxy::CANCELLED() : PaymentStatusProxy::AUTHORIZED();
                break;

                default:
                    $this->status = PaymentStatusProxy::PENDING();
            }
        }

        return $this->status;
    }

    public function getNativeStatus(): Enum
    {
        return $this->nativeStatus;
    }

    private function calculateAmountPaid(?float $amountPaid, AdyenEvent $nativeStatus): ?float
    {
        if ($nativeStatus->hasFailed() || $nativeStatus->isUnknown()) {
            return null;
        }

        if (in_array($nativeStatus->value(), self::$eventsWithNegativeAmount)) {
            return -1 * $amountPaid;
        } elseif (in_array($nativeStatus->value(), self::$eventsWithPositiveAmount)) {
            return $amountPaid;
        }

        return null;
    }
}

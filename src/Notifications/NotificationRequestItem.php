<?php

declare(strict_types=1);

/**
 * Contains the NotificationRequestItem class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-13
 *
 */

namespace Vanilo\Adyen\Notifications;

use Illuminate\Support\Carbon;
use Vanilo\Adyen\Models\AdyenEvent;

final class NotificationRequestItem
{
    use UnderstandsStringBooleans;
    
    private array $rawData;

    private AdyenEvent $event;

    private ?string $pspReference;

    private Carbon $date;

    private string $merchantAccountCode;

    private string $merchantReference;

    private float $amount;

    private string $currency;

    private ?string $originalReference;

    private ?string $reason;

    private AdditionalData $additionalData;

    private function __construct(array $rawData)
    {
        $this->event = AdyenEvent::create($rawData['eventCode']);
        $this->rawData = $rawData;
    }

    public static function createFromPayload(array $payload): self
    {
        $data = $payload['NotificationRequestItem'];
        $result = new self($data);

        self::boolify($data['success']) ? $result->event->markAsSuccessful() : $result->event->markAsFailed();
        $result->date = Carbon::parse($data['eventDate']);
        $result->pspReference = $data['pspReference'] ?? null;
        $result->merchantAccountCode = $data['merchantAccountCode'] ?? '';
        $result->merchantReference = $data['merchantReference'] ?? '';
        $result->amount = ($data['amount']['value'] ?? 0) / 100; // @todo, some currencies might not be in "cents";
        $result->currency = $data['amount']['currency'];
        $result->originalReference = $data['originalReference'] ?? null;
        $result->reason = $data['reason'] ?? null;
        $result->additionalData = new AdditionalData($data['additionalData'] ?? []);

        return $result;
    }

    public function event(): AdyenEvent
    {
        return $this->event;
    }

    public function date(): Carbon
    {
        return $this->date;
    }

    public function merchantAccountCode(): string
    {
        return $this->merchantAccountCode;
    }

    public function pspReference(): ?string
    {
        return $this->pspReference;
    }

    public function merchantReference(): string
    {
        return $this->merchantReference;
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    /**
     * Certain event types, but not all of them contain this field
     * @see https://docs.adyen.com/development-resources/webhooks/understand-notifications#event-codes
     */
    public function originalReference(): ?string
    {
        return $this->originalReference;
    }

    /**
     * Certain event types, but not all of them contain this field
     * @see https://docs.adyen.com/development-resources/webhooks/understand-notifications#event-codes
     */
    public function reason(): ?string
    {
        return $this->reason;
    }

    public function additionalData(): AdditionalData
    {
        return $this->additionalData;
    }

    public function toArray(): array
    {
        return $this->rawData;
    }
}

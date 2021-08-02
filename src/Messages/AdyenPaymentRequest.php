<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Messages;

use Illuminate\Support\Facades\View;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentRequest;

class AdyenPaymentRequest implements PaymentRequest
{
    private string $view = 'adyen::_request';

    private string $clientKey;

    private string $environment;

    private Payment $payment;

    private array $paymentMethods;

    public function __construct(
        Payment $payment,
        array $paymentMethods,
        string $clientKey,
        string $environment
    ) {
        $this->clientKey = $clientKey;
        $this->environment = $environment;
        $this->payment = $payment;
        $this->paymentMethods = $paymentMethods;
    }

    public function getHtmlSnippet(array $options = []): ?string
    {
        return View::make(
            $this->view,
            [
                'clientKey' => $this->clientKey,
                'environment' => $this->environment,
                'locale' => $options['locale'] ?? app()->getLocale(),
                'paymentMethods' => $this->paymentMethods,
                'amount' => [
                    'amount' => $this->payment->getAmount() * 100, // @todo, some currencies might not be in "cents"
                    'currency' => $this->payment->getCurrency(),
                ]
            ]
        )->render();
    }

    public function willRedirect(): bool
    {
        return false;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }
}

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

    private string $submitUrl;

    private string $detailsUrl;

    private string $returnUrl;

    private ?string $locale;

    public function __construct(
        Payment $payment,
        array $paymentMethods,
        string $clientKey,
        string $environment,
        ?string $locale,
        array $urls = []
    ) {
        $this->clientKey = $clientKey;
        $this->environment = $environment;
        $this->payment = $payment;
        $this->paymentMethods = $paymentMethods;
        $this->locale = $locale;
        $this->submitUrl = $urls['submit'] ?? '';
        $this->detailsUrl = $urls['details'] ?? '';
        $this->returnUrl = $urls['return'] ?? '';
    }

    public function getHtmlSnippet(array $options = []): ?string
    {
        $view = $options['view'] ?? $this->view;

        return View::make(
            $view,
            [
                'clientKey' => $this->clientKey,
                'environment' => $this->environment,
                'locale' => $this->locale,
                'paymentMethods' => $this->paymentMethods,
                'payment' => $this->payment,
                'submitUrl' => $this->submitUrl,
                'returnUrl' => $this->returnUrl,
                'detailsUrl' => $this->detailsUrl,
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

    public function getRemoteId(): ?string
    {
        return null;
    }
}

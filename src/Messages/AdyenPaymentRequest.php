<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Messages;

use Illuminate\Support\Facades\View;
use Vanilo\Payment\Contracts\PaymentRequest;

class AdyenPaymentRequest implements PaymentRequest
{
    private string $view = 'adyen::_request';

    private string $clientKey;

    private string $environment;

    public function __construct(string $clientKey, string $environment)
    {
        $this->clientKey = $clientKey;
        $this->environment = $environment;
    }

    public function getHtmlSnippet(array $options = []): ?string
    {
        return View::make(
            $this->view,
            [
                'clientKey' => $this->clientKey,
                'locale' => $options['locale'] ?? app()->getLocale(),
                'environment' => $this->environment,
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

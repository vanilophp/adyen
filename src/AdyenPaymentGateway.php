<?php

declare(strict_types=1);

namespace Vanilo\Adyen;

use Illuminate\Http\Request;
use Vanilo\Adyen\Contracts\AdyenClient;
use Vanilo\Adyen\Factories\RequestFactory;
use Vanilo\Adyen\Factories\ResponseFactory;
use Vanilo\Contracts\Address;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\TransactionHandler;
use Vanilo\Payment\Support\ReplacesPaymentUrlParameters;

class AdyenPaymentGateway implements PaymentGateway
{
    use ReplacesPaymentUrlParameters;

    public const DEFAULT_ID = 'adyen';

    private static ?string $svg = null;

    private AdyenClient $adyenClient;

    private ?RequestFactory $requestFactory = null;

    private ?ResponseFactory $responseFactory = null;

    private string $submitUrl;

    private string $detailsUrl;

    private string $returnUrl;

    public function __construct(AdyenClient $adyenClient, string $submitUrl, string $detailsUrl, string $returnUrl)
    {
        $this->adyenClient = $adyenClient;
        $this->submitUrl = $submitUrl;
        $this->detailsUrl = $detailsUrl;
        $this->returnUrl = $returnUrl;
    }

    public static function getName(): string
    {
        return 'Adyen';
    }

    public function createPaymentRequest(Payment $payment, Address $shippingAddress = null, array $options = []): PaymentRequest
    {
        if (null === $this->requestFactory) {
            $this->requestFactory = new RequestFactory($this->adyenClient);
        }

        $urls = array_merge([
            'details' => $this->detailsUrl,
            'submit' => $this->submitUrl,
        ], $options['urls'] ?? []);
        $options['urls'] = $urls;

        return $this->requestFactory->create($payment, $options);
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        if (null === $this->responseFactory) {
            $this->responseFactory = new ResponseFactory($this->adyenClient);
        }

        return $this->responseFactory->createFromRequest($request);
    }

    public static function svgIcon(): string
    {
        return self::$svg ??= file_get_contents(__DIR__ . '/resources/logo.svg');
    }

    public function transactionHandler(): ?TransactionHandler
    {
        return null;
    }

    public function submitPaymentToAdyen(Payment $payment, $stateDataPaymentMethod)
    {
        return $this->adyenClient->submitPayment(
            $payment,
            $stateDataPaymentMethod,
            $this->replaceUrlParameters($this->returnUrl, $payment)
        );
    }

    public function isOffline(): bool
    {
        return false;
    }
}

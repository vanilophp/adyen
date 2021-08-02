<?php

declare(strict_types=1);

/**
 * Contains the PaymentRequestTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-02
 *
 */

namespace Vanilo\Adyen\Tests;

use Vanilo\Adyen\AdyenPaymentGateway;
use Vanilo\Adyen\Messages\AdyenPaymentRequest;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Models\PaymentMethod;

class PaymentRequestTest extends TestCase
{
    use CreatesDummyPayment;
    use MakesDummyAdyenConfiguration;

    private PaymentMethod $method;

    protected function setUp(): void
    {
        parent::setUp();

        $this->method = PaymentMethod::create([
            'gateway' => AdyenPaymentGateway::getName(),
            'name' => 'Adyen',
        ]);
    }

    /** @test */
    public function the_gateway_creates_a_payment_request_instance()
    {
        /** @var AdyenPaymentGateway $gateway */
        $gateway = app(AdyenPaymentGateway::class);
        $request = $gateway->createPaymentRequest($this->createDummyPayment());

        $this->assertInstanceOf(PaymentRequest::class, $request);
        $this->assertInstanceOf(AdyenPaymentRequest::class, $request);
    }

    /** @test */
    public function the_request_does_not_redirect()
    {
        $request = app(AdyenPaymentGateway::class)->createPaymentRequest($this->createDummyPayment());

        $this->assertFalse($request->willRedirect());
    }

    /** @test */
    public function the_request_can_render_the_html_snippet()
    {
        /** @var AdyenPaymentGateway $gateway */
        $gateway = app(AdyenPaymentGateway::class);
        $payment = $this->createDummyPayment();
        $request = $gateway->createPaymentRequest($payment);

        $html = $request->getHtmlSnippet();
        $this->assertIsString($html);
    }

    /** @test */
    public function the_html_snippet_contains_the_client_key()
    {
        $request = app(AdyenPaymentGateway::class)->createPaymentRequest($this->createDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('test_ClientKEY_444', $html);
    }

    /** @test */
    public function the_html_snippet_locale_defaults_to_the_apps_locale()
    {
        $request = app(AdyenPaymentGateway::class)->createPaymentRequest($this->createDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('locale: "' . app()->getLocale() . '"', $html);
    }

    /** @test */
    public function the_locale_can_be_specified()
    {
        $request = app(AdyenPaymentGateway::class)->createPaymentRequest($this->createDummyPayment());

        $html = $request->getHtmlSnippet(['locale' => 'de-AT']);
        $this->assertStringContainsString('locale: "de-AT"', $html);
    }

    /** @test */
    public function it_injects_the_proper_environment()
    {
        $request = app(AdyenPaymentGateway::class)->createPaymentRequest($this->createDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('environment: "test"', $html);

        config(['vanilo.adyen.is_test' => false]);
        $request = app(AdyenPaymentGateway::class)->createPaymentRequest($this->createDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('environment: "live"', $html);
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
        $this->setDummyAdyenConfiguration();
    }
}

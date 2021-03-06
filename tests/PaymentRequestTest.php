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
use Vanilo\Adyen\Tests\Concerns\CreatesCompleteDummyPayment;
use Vanilo\Adyen\Tests\Concerns\HasFakeAdyenClient;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Models\PaymentMethod;

class PaymentRequestTest extends TestCase
{
    use CreatesCompleteDummyPayment;
    use HasFakeAdyenClient;

    private PaymentMethod $method;

    private AdyenPaymentGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->method = PaymentMethod::create([
            'gateway' => AdyenPaymentGateway::getName(),
            'name' => 'Adyen',
        ]);

        $this->gateway = new AdyenPaymentGateway($this->fakeAdyenClient(), '', '', '');
    }

    /** @test */
    public function the_gateway_creates_a_payment_request_instance()
    {
        $request = $this->gateway->createPaymentRequest($this->createCompleteDummyPayment());

        $this->assertInstanceOf(PaymentRequest::class, $request);
        $this->assertInstanceOf(AdyenPaymentRequest::class, $request);
    }

    /** @test */
    public function the_request_does_not_redirect()
    {
        $request = $this->gateway->createPaymentRequest($this->createCompleteDummyPayment());

        $this->assertFalse($request->willRedirect());
    }

    /** @test */
    public function the_request_can_render_the_html_snippet()
    {
        $payment = $this->createCompleteDummyPayment();
        $request = $this->gateway->createPaymentRequest($payment);

        $html = $request->getHtmlSnippet();
        $this->assertIsString($html);
    }

    /** @test */
    public function the_html_snippet_contains_the_client_key()
    {
        $this->fakeAdyenClient()->setClientKey('TestClientKeyG1b76hnBg');
        $request = $this->gateway->createPaymentRequest($this->createCompleteDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('TestClientKeyG1b76hnBg', $html);
    }

    /** @test */
    public function the_html_snippet_contains_the_amount()
    {
        $request = $this->gateway->createPaymentRequest($this->createCompleteDummyPayment('USD', 12.99));

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('"amount":1299', $html);
        $this->assertStringContainsString('"currency":"USD"', $html);
    }

    /** @test */
    public function the_html_snippet_locale_defaults_to_the_apps_locale()
    {
        $request = $this->gateway->createPaymentRequest($this->createCompleteDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('locale: "' . app()->getLocale() . '"', $html);
    }

    /** @test */
    public function the_locale_can_be_specified()
    {
        $request = $this->gateway->createPaymentRequest(
            $this->createCompleteDummyPayment(),
            null,
            ['locale' => 'de-AT']
        );

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('locale: "de-AT"', $html);
    }

    /** @test */
    public function the_submit_url_can_be_specified()
    {
        $request = $this->gateway->createPaymentRequest(
            $this->createCompleteDummyPayment(),
            null,
            ['urls' => ['submit' => 'http://some.url.to/1234']]
        );

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('handleSubmission(state.data, "http://some.url.to/1234")', $html);
    }

    /** @test */
    public function the_details_url_can_be_specified()
    {
        $request = $this->gateway->createPaymentRequest(
            $this->createCompleteDummyPayment(),
            null,
            ['urls' => ['details' => 'https://ecom.app/adyen/details']]
        );

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('handleDetailsCall(state.data, "https://ecom.app/adyen/details")', $html);
    }

    /** @test */
    public function the_return_url_can_be_specified()
    {
        $this->fakeAdyenClient()->submitPayment(
            $this->createCompleteDummyPayment(),
            [],
            'https://ecom.app/checkout/adyen/return?pid=123'
        );

        $this->assertEquals(
            'https://ecom.app/checkout/adyen/return?pid=123',
            $this->fakeAdyenClient()->getAdyenCreatePaymentRequestData()['returnUrl']
        );
    }

    /** @test */
    public function url_parameters_are_replaced()
    {
        $payment = $this->createCompleteDummyPayment();
        $request = $this->gateway->createPaymentRequest(
            $payment,
            null,
            ['urls' => [
                'submit' => 'http://some.url.to/submit/{paymentId}',
                'details' => 'http://some.url.to/details?pid={paymentId}'
            ]]
        );

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('handleSubmission(state.data, "http://some.url.to/submit/' . $payment->getPaymentId() . '")', $html);
        $this->assertStringContainsString('handleDetailsCall(state.data, "http://some.url.to/details?pid=' . $payment->getPaymentId() . '")', $html);
    }

    /** @test */
    public function it_injects_the_proper_environment()
    {
        config(['vanilo.adyen.is_test' => true]);
        $request = $this->gateway->createPaymentRequest($this->createCompleteDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('environment: "test"', $html);

        config([
            'vanilo.adyen.is_test' => false,
            'vanilo.adyen.live_endpoint_url_prefix' => '1797a841fbb37ca7-TestECOM'
        ]);
        $request = $this->gateway->createPaymentRequest($this->createCompleteDummyPayment());

        $html = $request->getHtmlSnippet();
        $this->assertStringContainsString('environment: "live"', $html);
    }
}

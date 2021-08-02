<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use Vanilo\Adyen\AdyenPaymentGateway;
use Vanilo\Adyen\Client\RealAdyenClient;
use Vanilo\Adyen\Contracts\AdyenClient;
use Vanilo\Payment\PaymentGateways;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    public function boot()
    {
        parent::boot();

        if ($this->config('gateway.register', true)) {
            PaymentGateways::register(
                $this->config('gateway.id', AdyenPaymentGateway::DEFAULT_ID),
                AdyenPaymentGateway::class
            );
        }

        if ($this->config('bind', true)) {
            $this->app->bind(RealAdyenClient::class, function ($app) {
                return new RealAdyenClient(
                    $this->config('api_key'),
                    $this->config('merchant_account'),
                    $this->config('client_key'),
                    $this->config('live_endpoint_url_prefix'),
                    $this->config('is_test'),
                );
            });

            $this->app->bind(AdyenClient::class, RealAdyenClient::class);

            $this->app->bind(AdyenPaymentGateway::class, function ($app) {
                return new AdyenPaymentGateway(
                    $app->make(AdyenClient::class)
                );
            });
        }

        $this->publishes([
            $this->getBasePath() . '/' . $this->concord->getConvention()->viewsFolder() =>
            resource_path('views/vendor/adyen'),
            'vanilo-adyen'
        ]);
    }
}

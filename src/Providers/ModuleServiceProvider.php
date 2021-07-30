<?php

declare(strict_types=1);

namespace Vanilo\Adyen\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use Vanilo\Payment\PaymentGateways;
use Vanilo\Adyen\AdyenPaymentGateway;

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
            $this->app->bind(AdyenPaymentGateway::class, function ($app) {
                return new AdyenPaymentGateway(
                    $this->config('xxx') // @todo replace with real
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

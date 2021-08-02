# Configuration

## Dotenv Values

The following `.env` parameters can be set in order to work with this package.

```dotenv
ADYEN_IS_TEST=false               # true for test, false for live environments
ADYEN_API_KEY="AQEjhmfuXNWTK0..." # Find at Adyen Customer Area -> Developers -> API credentials
ADYEN_MERCHANT_ACCOUNT="YourECOM" # Obtain the merchant key from Adyen Customer Area
ADYEN_CLIENT_KEY="test_WDLE44..." # Find at Adyen Customer Area -> Developers -> API credentials
ADYEN_LIVE_ENDPOINT_URL_PREFIX="1797a841fbb37ca7-AdyenDemo" # Only needed for live 
```

> For more details refer to [Adyen Getting Started](https://docs.adyen.com/online-payments/get-started)
> and [Live URL Prefix](https://docs.adyen.com/development-resources/live-endpoints#live-url-prefix).

## Registration with Payments Module

The module will automatically register the payment gateway with the Vanilo Payments registry by
default. Having that, you can get a gateway instance directly from the Payment registry:

```php
$gateway = \Vanilo\Payment\PaymentGateways::make('adyen');
```

### Registering With Another Name

If you'd like to use another name in the payment registry, it can be done in the module config:

```php
//config/concord.php
return [
    'modules' => [
        //...
        Vanilo\Adyen\Providers\ModuleServiceProvider::class => [
            'gateway' => [
                'id' => 'adyen-gateway'
            ]
        ]
        //...
    ]
];
```

After this you can obtain a gateway instance with the configured name:

```php
\Vanilo\Payment\PaymentGateways::make('adyen-gateway');
```

### Prevent from Auto-registration

If you don't want it to be registered automatically, you can prevent it by changing the module
configuration:

```php
//config/concord.php
return [
    'modules' => [
        //...
        Vanilo\Adyen\Providers\ModuleServiceProvider::class => [
            'gateway' => [
                'register' => false
            ]
        ]
        //...
    ]
];
```

### Manual Registration

If you disable registration and want to register the gateway manually you can do it by using the
Vanilo Payment module's payment gateway registry:

```php
use Vanilo\Adyen\AdyenPaymentGateway;
use Vanilo\Payment\PaymentGateways;

PaymentGateways::register('whatever-name-you-want', AdyenPaymentGateway::class);
```

## Binding With The Laravel Container

By default `AdyenPaymentGateway::class` gets bound to the Laravel DI container, so that you can
obtain a properly autoconfigured instance. Typically, you don't get the instance directly from the
Laravel container (ie. `app()->make(AdyenPaymentGateway::class)`) but from the Vanilo Payment
Gateway registry:

```php
$instance = \Vanilo\Payment\PaymentGateways::make('adyen');
```

The default DI binding happens so that all the configuration parameters are read from the config values
mentioned above. This will work out of the box and will be sufficient for most of the applications.

### Manual Binding

It is possible to prevent the automatic binding and thus configure the Gateway in a custom way in
the module config:

```php
//config/concord.php
return [
    'modules' => [
        Vanilo\Adyen\Providers\ModuleServiceProvider::class => [
            'bind' => false,
        //...
    ]
    //...
];
```

This can be useful if the Gateway configuration can't be set in the env file, for example when:

- The credentials can be **configured in an Admin interface** instead of `.env`
- Your app has **multiple payment methods** that use Adyen with **different credentials**
- There is a **multi-tenant application**, where each tenant has their own credentials

Setting `vanilo.adyen.bind` to `false` will cause that neither the
`AdyenClient` nor the `AdyenPaymentGateway` classes will be bound with
the Laravel DI container automatically. Therefore, you need to do this
yourself in your application, typically in the
`AppServiceProvider::boot()` method:

```php
$this->app->bind(AdyenClient::class, function ($app) {
    return new AdyenClient(
        config('vanilo.adyen.api_key'),
        config('vanilo.adyen.merchant_account'),
        config('vanilo.adyen.client_key'),
        config('vanilo.adyen.live_endpoint_url_prefix'),
        config('vanilo.adyen.is_test'),
    );
});

$this->app->bind(AdyenPaymentGateway::class, function ($app) {
    return new AdyenPaymentGateway(
        $app->make(AdyenClient::class)
    );
});
```

---

**Next**: [Workflow &raquo;](workflow.md)

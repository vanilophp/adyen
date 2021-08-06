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

## Setting URLs

Adyen requires your application to have 3 endpoints that can be called
by Adyen's Drop-in frontend: the **submit**, the **details** and the
**return** URL.

These URLs are not defined by this library, but by your application.
Routes, controllers, etc need to be set up in your app and be passed to
this library by setting the `vanilo.adyen.urls.submit`,
`vanilo.adyen.urls.details` and `vanilo.adyen.urls.return` configuration
values.

To set these values, add them in your app to `config/vanilo.php`:

```php
// config/vanilo.php
// Values below are examples, the URI structure is completely up to you
return [
    //...
    'adyen' => [
        'urls' => [
            'details' => '/payment/adyen/{paymentId}/details',
            'submit' => '/payment/adyen/{paymentId}/submit',
            'return' => '/checkout/adyen-return',
        ],        
    ]
    //...
];
```

In the [Examples](examples.md) section of this documentation, there's a
complete example how to implement these endpoints.

### The Submit URL

> See also in Adyen Documentation at [Make a Payment](https://docs.adyen.com/online-payments/drop-in-web?tab=script_2#step-3-make-a-payment).

The "Submit URL" is an endpoint in your application that gets called by
the Adyen frontend once a customer has completed the payment details and
clicks the "Pay" button.

To configure it you need to set the config value of `vanilo.adyen.urls.submit`:

```php
// config/vanilo.php
return [
    'adyen' => [
        'urls' => [
            'submit' => '/checkout/adyen-submit/{paymentId}',
        ],        
    ]
];
```

See at [Examples](examples.md) how to implement this endpoint.

### The Details URL

> See also in Adyen Documentation at [Additional Payment Details](https://docs.adyen.com/online-payments/drop-in-web?tab=script_2#step-5-additional-payment-details).

Some payment methods require additional action from the shopper such as:
- to scan a QR code,
- to authenticate a payment with 3D Secure, or
- to log in to their bank's website, etc

to complete the payment. If the shopper performed such an action, the
frontend has to send these additional details to an endpoint at your
application's backend that has to handle these details.

To configure it you need to set the config value of `vanilo.adyen.urls.details`:

```php
// config/vanilo.php
return [
    'adyen' => [
        'urls' => [
            'details' => '/checkout/adyen/details/{paymentId}',
        ],        
    ]
];
```

See at [Examples](examples.md) how to implement this endpoint.

### The Return URL

> See also in Adyen Documentation at [Handle the redirect result](https://docs.adyen.com/online-payments/drop-in-web?tab=script_2#handle-the-redirect-result).

Certain payments require Adyen to redirect the shopper to another
webpage (eg. to the card's issuer bank for 3D Secure authentication).
At the end of the operation the shopper will be sent back to your shop,
to a URL that you specify. Adyen will append the Base64-encoded result
value as the `redirectResult` GET parameter.

To specify the return URL, you need to set the config value of `vanilo.adyen.urls.return`:

```php
// config/vanilo.php
return [
    'adyen' => [
        'urls' => [
            'return' => '/shop/checkout/payment/{paymentId}/return',
        ],        
    ]
];
```

See at [Examples](examples.md) how to implement this endpoint.

### Webhook URL

You'll also need a webhook endpoint, but since its URL has to be entered
manually in the Adyen Customer Area, there's no need to specify it in
the configuration.

See at [Examples](examples.md) how to implement this endpoint.

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

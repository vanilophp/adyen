# Examples

The Example below shows parts of the code that you can put in your application.

### CheckoutController

The controller below processes a submitted checkout, prepares the payment and returns the thank you
page with the prepared payment request:

```php
use Illuminate\Http\Request;
use Vanilo\Framework\Models\Order;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethod;
use Vanilo\Payment\PaymentGateways;

class CheckoutController
{
    public function store(Request $request)
    {
        $order = Order::createFrom($request);
        $paymentMethod = PaymentMethod::find($request->get('paymentMethod'));
        $payment = PaymentFactory::createFromPayable($order, $paymentMethod);
        $gateway = PaymentGateways::make('adyen');
        $paymentRequest = $gateway->createPaymentRequest($payment);
        
        return view('checkout.thank-you', [
            'order' => $order,
            'paymentRequest' => $paymentRequest
        ]);
    }
}
```

### checkout/thank-you.blade.php

This sample blade template contains a thank you page where you can render the payment initiation
form:

**Blade Template:**

```blade
@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Thank you</h1>

        <div class="alert alert-success">Your order has been registered with number
            <strong>{{ $order->getNumber() }}</strong>.
        </div>

        <h3>Payment</h3>

        {!! $paymentRequest->getHtmlSnippet(); !!}
    </div>
@endsection
```

### AdyenReturnController

```php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vanilo\Payment\Models\Payment;
use Vanilo\Payment\PaymentGateways;
use Vanilo\Payment\Processing\PaymentResponseHandler;


class AdyenReturnController extends Controller
{
    public function submit(Request $request, string $paymentId)
    {
        $gateway = PaymentGateways::make('adyen');
        $payment = Payment::findByPaymentId($paymentId);
    
        return $gateway->submitPaymentToAdyen($payment, $request->paymentMethod);
    }
    
    public function return(Request $request)
    {
        Log::debug('Adyen confirmation', $request->toArray());

        $payment = $this->processPaymentResponse($request);

        return view('payment.return', [
            'payment'  => $payment,
            'order'    => $payment->getPayable()
        ]);
    }
    
    public function webhook(Request $request)
    {
        Log::debug('Adyen webhook', [
            'req' => $request->toArray(),
            'method' => $request->method(),
        ]);

        $this->processPaymentResponse($request);

        return new JsonResponse(['message' => 'Received OK']);
    }

    private function processPaymentResponse(Request $request): Payment
    {
        $response = PaymentGateways::make('adyen')->processPaymentResponse($request);
        $payment  = Payment::findByPaymentId($response->getPaymentId());

        if (!$payment) {
            throw new ModelNotFoundException('Could not locate payment with id ' . $response->getPaymentId());
        }

        $handler = new PaymentResponseHandler($payment, $response);
        $handler->writeResponseToHistory();
        $handler->updatePayment();
        $handler->fireEvents();

        return $payment;
    }
}
```

### Routes

The routes for Stro[e should look like:

```php
//web.php
Route::group(['prefix' => 'payment/adyen', 'as' => 'payment.adyen.'], function() {
    Route::get('return', 'AdyenReturnController@return')->name('return');
    Route::get('webhook', 'adyenReturnController@webhook')->name('webhook');
});
```

**IMPORTANT!**: Make sure to **disable CSRF verification** for these URLs, by adding them as
exceptions to `app/Http/Middleware/VerifyCsrfToken`:

```php
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/payment/adyen/*'
    ];
}
```

Have fun!

---
Congrats, you've reached the end of this doc! 🎉

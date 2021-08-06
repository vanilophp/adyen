<div id="adyen-dropin-container"></div>
<script src="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.2/adyen.js"
        integrity="sha384-hhvkW03dAUXGdLB8iXXHW311qerCuAJvbU4LxZZzMP5knSYEFWAenVr0wa5ulKwO"
        crossorigin="anonymous"></script>

<link rel="stylesheet"
      href="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.2/adyen.css"
      integrity="sha384-dkJjySvUD62j8VuK62Z0VF1uIsoa+APxWLDHpTjBRwQ95VxNl7oaUvCL+5WXG8lh"
      crossorigin="anonymous">

<script>
    async function callBackend(url, data) {
        const res = await fetch(url, {
            method: "POST",
            body: data ? JSON.stringify(data) : "",
            headers: {
                "Content-Type": "application/json",
            },
        });

        return await res.json();
    }

    async function handleSubmission(data, url) {
        return callBackend(url, data);
    }

    function showFinalResult(response, dropin) {
        switch (response.resultCode) {
            case "Authorised":
                dropin.setStatus('success');
                break;
            case "Pending":
            case "Received":
                dropin.setStatus('loading', { message: '{{ __('Transaction is being processed...') }}'});
                break;
            case "Refused":
                dropin.setStatus('error', { message: '{{ __('The payment has been rejected') }}'});
                setTimeout(function () {
                    dropin.setStatus('ready');
                }, 2700);
                break;
            default:
                dropin.setStatus('error');
                setTimeout(function () {
                    dropin.setStatus('ready');
                }, 2700);
                break;
        }
    }

    const adyenConfiguration = {
        paymentMethodsResponse: {!! json_encode($paymentMethods) !!},
        clientKey: "{{ $clientKey }}",
        locale: "{{ $locale }}",
        environment: "{{ $environment }}",
        amount: {!! json_encode($amount) !!},
        onSubmit: (state, dropin) => {
            handleSubmission(state.data, "{{ $submitUrl }}")
                .then(response => {
                    if (response.action) {
                        // Drop-in handles the action object from the /payments response
                        dropin.handleAction(response.action);
                    } else {
                        // Your function to show the final result to the shopper
                        showFinalResult(response, dropin);
                    }
                })
                .catch(error => {
                    throw Error(error);
                });
        },
        onAdditionalDetails: (state, dropin) => {
            // Your function calling your server to make a `/payments/details` request
            handleDetailsCall(state.data, "{{ $detailsUrl }}")
                .then(response => {
                    if (response.action) {
                        // Drop-in handles the action object from the /payments response
                        dropin.handleAction(response.action);
                    } else {
                        // Your function to show the final result to the shopper
                        showFinalResult(response);
                    }
                })
                .catch(error => {
                    throw Error(error);
                });
        }
    };

    const checkout = new AdyenCheckout(adyenConfiguration);
    const dropin = checkout
        .create('dropin', {
            openFirstPaymentMethod: false
        })
        .mount('#adyen-dropin-container');
</script>
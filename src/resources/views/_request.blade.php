<div id="adyen-dropin-container"></div>
<script src="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.2/adyen.js"
        integrity="sha384-hhvkW03dAUXGdLB8iXXHW311qerCuAJvbU4LxZZzMP5knSYEFWAenVr0wa5ulKwO"
        crossorigin="anonymous"></script>

<link rel="stylesheet"
      href="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.2/adyen.css"
      integrity="sha384-dkJjySvUD62j8VuK62Z0VF1uIsoa+APxWLDHpTjBRwQ95VxNl7oaUvCL+5WXG8lh"
      crossorigin="anonymous">

<script>
    const adyenConfiguration = {
        paymentMethodsResponse: paymentMethodsResponse, // The `/paymentMethods` response from the server.
        clientKey: "{{ $clientKey }}", // Web Drop-in versions before 3.10.1 use originKey instead of clientKey.
        locale: "{{ $locale }}",
        environment: "{{ $environment }}",
        onSubmit: (state, dropin) => {
            // Your function calling your server to make the `/payments` request
            makePayment(state.data)
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
        },
        onAdditionalDetails: (state, dropin) => {
            // Your function calling your server to make a `/payments/details` request
            makeDetailsCall(state.data)
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
        },
        paymentMethodsConfiguration: {
            card: { // Example optional configuration for Cards
                hasHolderName: true,
                holderNameRequired: true,
                enableStoreDetails: true,
                hideCVC: false, // Change this to true to hide the CVC field for stored cards
                name: 'Credit or debit card',
                onSubmit: () => {
                }, // onSubmit configuration for card payments. Overrides the global configuration.
            }
        }
    };

    const checkout = new AdyenCheckout(adyenConfiguration);
    const dropin = checkout
        .create('dropin', {
            openFirstPaymentMethod: false
        })
        .mount('#adyen-dropin-container');
</script>
document.addEventListener("DOMContentLoaded", (event) => {
    const stripe_public_key = document.getElementById('stripe_public_key').value;
        console.log('stripe_public_key', stripe_public_key)
    const paymentsClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });
        console.log('paymentsClient', paymentsClient)


// Step 1: Define your Google Pay API version
    const baseRequest = {
        apiVersion: 2,
        apiVersionMinor: 0
    };

// Step 2: Request a payment token for your payment provider
// const tokenizationSpecification = {
//     type: 'PAYMENT_GATEWAY',
//     parameters: {
//         'gateway': 'example',
//         'gatewayMerchantId': 'exampleGatewayMerchantId'
//     }
// };

    const tokenizationSpecification = {
        type: 'PAYMENT_GATEWAY',
        parameters: {
            "gateway": "stripe",
            "stripe:version": "2018-10-31",
            "stripe:publishableKey": stripe_public_key
        }
    };

// Step 3: Define supported payment card networks
    const allowedCardNetworks = ["AMEX", "DISCOVER", "INTERAC", "JCB", "MASTERCARD", "VISA"];
    const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];

// Step 4: Describe your allowed payment methods
    const baseCardPaymentMethod = {
        type: 'CARD',
        parameters: {
            allowedAuthMethods: allowedCardAuthMethods,
            allowedCardNetworks: allowedCardNetworks
        }
    };
    const cardPaymentMethod = Object.assign(
        {tokenizationSpecification: tokenizationSpecification},
        baseCardPaymentMethod
    );

    const isReadyToPayRequest = Object.assign({}, baseRequest);
    isReadyToPayRequest.allowedPaymentMethods = [baseCardPaymentMethod];

// Step 8: Create a PaymentDataRequest object
    const paymentDataRequest = Object.assign({}, baseRequest);
    paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
    paymentDataRequest.transactionInfo = {
        totalPriceStatus: 'FINAL',
        totalPrice: '123.45',
        currencyCode: 'USD',
        countryCode: 'US'
    };
    paymentDataRequest.merchantInfo = {
        merchantName: 'Reed Events',
        merchantId: '12345678901234567890'
    };

    paymentsClient.isReadyToPay(paymentDataRequest)
        .then(function(response) {
            if (response.result) {
                console.log('add a Google Pay payment button')
                const button =
                    paymentsClient.createButton({onClick: () => {
                            paymentsClient.loadPaymentData(paymentDataRequest).then(function(paymentData) {
                                // Handle the response
                                const token = paymentData.paymentMethodData.tokenizationData.token;
                                console.log('token', token)
                                var post_data = new FormData();
                                post_data.append('token', token)
                                // Send this token to your server
                                // fetch('http://localhost/phphub/php/Payment/GooglePay/stripe_charge.php', {
                                //     method: 'POST',
                                //     headers: {
                                //         'Content-Type': 'application/json',
                                //     },
                                //     body: post_data,
                                // })
                                //     .then((response) => response.json())
                                //     .then((data) => {
                                //         console.log('payment_response', data)
                                //     })
                                // ;

                                $.ajax({
                                    url: 'http://localhost/phphub/php/Payment/GooglePay/stripe_charge.php',
                                    method: 'POST',
                                    data: {
                                        token: token
                                    },
                                    //async: false,
                                    success: function (response) {
                                        try {
                                            var resp = JSON.parse(response);
                                            console.log('payment_response: ', resp);
                                        }
                                        catch (e) {
                                            console.error('error', e)
                                        }
                                    },
                                    error: function (xhr, status) {

                                    },
                                })


                            }).catch(function(err) {
                                // Handle the error
                                console.error(err);
                            });
                        },
                        allowedPaymentMethods: []}); // same payment methods as for the loadPaymentData() API call
                document.getElementById('container').appendChild(button);
            }
        })
        .catch(function(err) {
            console.log('show error in developer console for debugging')
            console.error(err);
        });

});
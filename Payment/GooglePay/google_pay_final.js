document.addEventListener("DOMContentLoaded", (event) => {
    const stripe_public_key = document.getElementById('stripe_public_key').value;
        console.log('stripe_public_key', stripe_public_key)
    const paymentsClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });
        console.log('paymentsClient', paymentsClient)

    const paymentDataRequest = {
        apiVersion: 2,
        apiVersionMinor: 0,
        allowedPaymentMethods: [
            {
                type: 'CARD',
                parameters: {
                    allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                    allowedCardNetworks: ['MASTERCARD', 'VISA'],
                },
                tokenizationSpecification: {
                    type: 'PAYMENT_GATEWAY',
                    parameters: {
                        'gateway': 'stripe',
                        'stripe:version': '2018-10-31',
                        'stripe:publishableKey': stripe_public_key
                    },
                },
            },
        ],
        merchantInfo: {
            merchantName: 'Example Merchant',
            merchantId: '12345678901234567890'
        },
        transactionInfo: {
            totalPriceStatus: 'FINAL',
            totalPrice: '123.45',
            currencyCode: 'USD',
            countryCode: 'US'
        },
        shippingAddressRequired: true,
        shippingOptionRequired: true,
        emailRequired: true
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
                                // Send this token to your server
                                fetch('/stripe_charge.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({ token: token }),
                                })
                                    .then((response) => response.json())
                                    .then((data) => {
                                        console.log('payment_response', data)
                                    })
                                ;


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
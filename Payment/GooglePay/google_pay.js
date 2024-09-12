function googlePay() {
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
                    allowedCardNetworks: ["AMEX", "DISCOVER", "INTERAC", "JCB", "MASTERCARD", "VISA"]
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
            merchantName: 'Reed Events',
            merchantId: '12345678901234567890'
        },
        transactionInfo: {
            totalPriceStatus: 'FINAL',
            totalPrice: '123.45',
            currencyCode: 'USD',
            countryCode: 'US'
        },
    }
    console.log('paymentDataRequest', paymentDataRequest)
    paymentsClient.isReadyToPay(paymentDataRequest)
        .then(function(response) {
            if (response.result) {
                console.log('add a Google Pay payment button')
                const button =
                    paymentsClient.createButton({onClick: () => {
                            paymentsClient.loadPaymentData(paymentDataRequest).then(function(paymentData) {
                                // Handle the response
                                const token = paymentData.paymentMethodData.tokenizationData.token;
                                const token_object = JSON.parse(token);
                                console.log('token_object', token_object)
                                $.ajax({
                                    url: 'http://localhost/phphub/php/Payment/GooglePay/stripe_charge.php',
                                    method: 'POST',
                                    data: {
                                        token_id: token_object['id']
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
                document.getElementById('google-pay-btn-container').appendChild(button);
            }
        })
        .catch(function(err) {
            console.log('show error in developer console for debugging')
            console.error(err);
        });
}
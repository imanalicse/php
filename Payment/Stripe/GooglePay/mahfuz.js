console.log('google_pay2')
document.addEventListener("DOMContentLoaded", (event) => {
    var renderGPay = function () {
        const stripe_public_key = document.getElementById('stripe_public_key').value;
        console.log('stripe_public_key', stripe_public_key)
        const button = document.querySelector('#google-checkout-pay');
        button.paymentRequest = {
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
                // countryCode: "${countryCode}",
                // currencyCode: "${AppUtil.siteCurrency.code.toUpperCase()}",
                // totalPriceStatus: "FINAL",
                // totalPrice: "${grandTotal}",
                // totalPriceLabel: "Total"
                totalPriceStatus: 'FINAL',
                totalPrice: '123.45',
                currencyCode: 'USD',
                countryCode: 'US'
            },
            shippingAddressRequired: true,
            shippingOptionRequired: true,
            emailRequired: true
        };
        /*
        if (${isFreeShipping}) {
            button.paymentRequest.shippingOptionParameters = {
                defaultSelectedOptionId: 'free',
                shippingOptions: [
                    {
                        id: 'free',
                        label: 'Free shipping'
                    }
                ]
            }
        }
        else {
            var shippingCost = "${AppUtil.siteCurrency.symbol}${shippingCost}"
            button.paymentRequest.shippingOptionParameters = {
                defaultSelectedOptionId: 'cost',
                shippingOptions: [
                    {
                        id: 'cost',
                        label: 'Shipping cost ' + shippingCost + ' is applicable'
                    }
                ]
            }
        }
        */
        button.addEventListener('loadpaymentdata', event => {
            console.log('event.detail', event.detail)
            // window.location.href = app.baseUrl + "googlePayCheckout/paymentByToken?data=" + encodeURIComponent(JSON.stringify(event.detail));
        });
        button.addEventListener('error', event => {
            console.log('error ', event.error);
        });
        button.addEventListener('cancel', event => {
            console.log('cancelled', event.detail);
        });
    }
    renderGPay();
});
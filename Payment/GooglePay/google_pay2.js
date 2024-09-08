var renderGPay = function (){
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
                        'stripe:publishableKey': "${public_key}"
                    },
                },
            },
        ],
        merchantInfo: {
            merchantId: "${merchantId}",
            merchantName: "${merchantName}",
        },
        transactionInfo: {
            countryCode: "${countryCode}",
            currencyCode: "${AppUtil.siteCurrency.code.toUpperCase()}",
            totalPriceStatus: "FINAL",
            totalPrice: "${grandTotal}",
            totalPriceLabel: "Total"
        },
        shippingAddressRequired: true,
        shippingOptionRequired: true,
        emailRequired: true
    };
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
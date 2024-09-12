<?php
require '../../../global_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apple PAY</title>
</head>
<body>
<input type="hidden" id="stripe_public_key" value="<?php echo getenv('STRIPE_PUBLIC_KEY'); ?>">
<div class="apple-pay-express-checkout">
    <span id="apple-pay-button"></span>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">
        const stripe_public_key = document.getElementById('stripe_public_key').value;
        Stripe.setPublishableKey(stripe_public_key);
        Stripe.applePay.checkAvailability(function(available) {
            if (available) {
                document.getElementById('apple-pay-button').style.display = 'block';
            } else {
                document.getElementsByClassName('apple-pay-express-checkout')[0].classList.add("apple-pay-not-available");
            }
        });
        document.getElementById('apple-pay-button').addEventListener('click', beginApplePay);
        function beginApplePay() {
            var paymentRequest = {
                requestPayerName: true,
                requestPayerEmail: true,
                countryCode: '${countryCode}',
                currencyCode: '${AppUtil.siteCurrency.code.toUpperCase()}',
                requiredShippingContactFields: ['postalAddress','email', 'name'],
                total: {
                    label: 'Total',
                    amount: '${grandTotal}'
                },
                requestShipping: true,
            };
            var session = Stripe.applePay.buildSession(paymentRequest,
                function(result, completion) {
                    bm.ajax({
                        url: app.baseUrl + "applePayCheckout/paymentByToken",
                        dataType: "JSON",
                        data: {token: JSON.stringify(result)},
                        success: function (resp) {
                            if (resp.status == "success") {
                                completion(ApplePaySession.STATUS_SUCCESS);
                                var payData = resp.model.data.payment;
                                var urlData = "?amount=" + payData.amount + "&gatewayResponse=" + payData.gatewayResponse +
                                    "&payerInfo=" + payData.payerInfo + "&paymentRef=" + payData.paymentRef + "&success="
                                    + payData.success + "&trackInfo=" + payData.trackInfo;
                                window.location.href = app.baseUrl + resp.model.controller + "/" + resp.model.action + urlData;
                            } else {
                                console.log("payment failed")
                                completion(ApplePaySession.STATUS_FAILURE);
                            }
                        },
                        error: function () {
                            completion(ApplePaySession.STATUS_FAILURE);
                        }
                    })
                }, function(error) {
                    console.log(error.message);
                });

            session.oncancel = function() {
                console.log("User hit the cancel button in the payment window");
            };
            session.begin();
        }

    </script>
</div>
</body>
</html>
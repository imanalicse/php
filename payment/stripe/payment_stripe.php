<!DOCTYPE html>
<html>
    <head>
        <title>Stripe Payment</title>
        <script src="https://olive.doyour.events/js/jquery.min.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
    </head>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Roboto', sans-serif;
    }
    .input-box {
        margin-bottom: 15px;
    }
    .field {
        box-sizing: border-box;
        padding: 10px;
        border-radius: 3px;
        border: 1px solid #d3dbde;
        width: 100%;
    }
    .input-box label {
        color: #8b8e9a;
        display: block;
        padding-bottom: 5px;
        font-size: 11px;
        font-weight: 600;
    }
    .StripeElement--focus {
        border: 1px solid #22B0AF;
    }
    .StripeElement--invalid {
        border-color: #f97070;
    }
    .StripeElement--complete {
        border-color: #22B0AF;
    }
    .StripeElement--webkit-autofill {
        background-color: #fff !important;
    }
    .button-container {
        text-align: center;
    }
    #card-errors {
        font-weight: bold;
        color: #f97070;
        text-align: center;
        margin: 15px 0;
        font-size: 14px;
    }
    .securepay-card-form {
        background: #fff;
        padding: 30px 30px 30px;
        border: 1px solid #eee;
        max-width: 362px;
        width: 100%;
    }
    .securepay-card-form .form-group > label {
        font-size: 14px;
    }
</style>
<body>
    <?php
        include "include.php";
        $stripe_pk = getenv("STRIPE_SK_TEST_KEY");
        $stripe_connect_id = getenv("STRIPE_ACCOUNT_ID");
    ?>
    <form action="/charge" method="post" id="payment-form" class="mb-3 securepay-card-form">
        <h4 class="pb-2 text-center">Amount : <?php echo "10"; ?></h4>

        <div class="mb-3 pt-3 pb-3 border-top d-flex justify-content-between align-items-center">
            <h6 class="color-black mb-0">Accepted Card </h6>
            <img class="card-icon-group" src="/images/visa-master-card.png" alt="card">
        </div>

        <div id="stripe-ui-container" class="stripe-card-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Card number</label>
                        <div id="card-number-element" class="field"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Expire date</label>
                        <div id="card-expiry-element" class="field"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>CVC</label>
                        <div id="card-cvc-element" class="field"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="card-errors" role="alert"></div>

        <div class="button-container"><button class="btn-normal font-18 full-width mt-4 radius-5">Confirm & Pay</button></div>
    </form>
</body>
<script type="text/javascript">
    var stripe = Stripe('<?php echo $stripe_pk; ?>', {
        stripeAccount: '<?php echo $stripe_connect_id; ?>'
    });
    var elements = stripe.elements();
    style = {
        base: {
            color: "#000",
            fontFamily: 'Open Sans, sans-serif',
            fontSize: '13px',
            fontWeight: 'normal',
            lineHeight: 'normal',
            height: 'auto'
        },
        '::placeholder': {
            color: '#ccc'
        },
        valid: {
            color: '#000'
        },
        invalid: {
            color: '#000'
        }
    };
    var cardNumberElement = elements.create('cardNumber', {
        style: style
    });
    cardNumberElement.mount('#card-number-element');
    var cardExpiryElement = elements.create('cardExpiry', {
        style: style
    });
    cardExpiryElement.mount('#card-expiry-element');
    var cardCvcElement = elements.create('cardCvc', {
        style: style
    });
    cardCvcElement.mount('#card-cvc-element');
    cardNumberElement.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var cardData = {
            // address_zip: document.getElementById('postal-code').value
        };
        stripe.createToken(cardNumberElement, cardData).then(function(result) {
            if (result.error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
                console.log(errorElement.textContent);
            } else {
                makePayment(result.token);
            }
        });
    });

    var making_payment = false;
    function makePayment(token) {
        if(making_payment==true){
            return false;
        }
        $("#stripe-pay-now-btn").prop('disabled', true).addClass('disabled');
        showEbLoading();

        var payment_success = false;

        setTimeout(function(data){
            $.ajax({
                url: '<?php echo  $this->EbUrl->build(APP_ROUTE_PREFIX.'/checkouts/make-stripe-payment') ?>?_t='+ new Date().getTime(),
                method: 'POST',
                data: {charge_token:token},
                dataType: 'html',
                async: false,
                success: function (data) {
                    //console.log(data);

                    if(typeof data != 'object'){
                        try{
                            data = $.parseJSON(data)
                        } catch (e) {

                        }
                    }

                    if(data.status){
                        if(data.status == 'error'){
                            if(!data.message || $.trim(data.message)==''){
                                data.message = 'Payment Failed.';
                            }
                            hideEbLoading();
                            Eb_swal(data.message, "" , "error");
                            $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                        }
                        else if(data.status=='success'){
                            if(!data.message || $.trim(data.message)==''){
                                data.message = 'Payment Success.';
                            }

                            Eb_swal(data.message, "" , "success");

                            if(data.redirect_url){
                                payment_success = true;
                                location.href = data.redirect_url;
                            }
                        } else {
                            hideEbLoading();
                            data.message = 'Payment Failed.';
                            Eb_swal(data.message, "" , "error")
                            $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                        }
                    } else {
                        hideEbLoading();
                        Eb_swal('Payment Failed', "" , "error")
                        $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                    }
                },
                error: function (xhr, status) {
                    making_payment = false;
                    hideEbLoading();
                    $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                },
            })
            .done(function( data ) {
                if(payment_success === false){
                    making_payment = false;
                    hideEbLoading();
                    $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                }
            });
        },100);
    }
</script>




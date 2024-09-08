document.addEventListener("DOMContentLoaded", function () {
    const stripe_public_key = $("#stripe_public_key").val();
    console.log('stripe_public_key', stripe_public_key)

    const stripe = Stripe(stripe_public_key);
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
    $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
    $("#stripe-pay-now-additional-btn").prop('disabled', false).removeClass('disabled');
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

        $("#stripe-pay-now-btn").prop('disabled', true).addClass('disabled');
        $("#stripe-pay-now-additional-btn").prop('disabled', true).addClass('disabled');

        stripe.createToken(cardNumberElement, cardData).then(function(result) {
            if (result.error) {
                console.log(result.error);
                $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                $("#stripe-pay-now-additional-btn").prop('disabled', false).removeClass('disabled');

                if (result.error.code === "account_invalid") {
                    alert('We are unable to process your request at this time. Please contact organiser.');
                }
                else {
                    alert(result.error.message)
                }
            }
            else {
                makePayment(result.token);
            }
        });
    });
});

function makePayment(token_info) {
    console.log('payment_token_info', token_info)

    $("#stripe-pay-now-btn").prop('disabled', true).addClass('disabled');
    $("#stripe-pay-now-additional-btn").prop('disabled', true).addClass('disabled');


    var payment_success = false;

    setTimeout(function(data){
        const stripe_payment_url = 'http://localhost/phphub/php/Payment/Stripe/StripePayment/stripe_charge.php?_t='+ new Date().getTime()
        $.ajax({
            url: stripe_payment_url,
            method: 'POST',
            data: {
                token_info: token_info
            },
            //async: false,
            success: function (response) {
                try {
                    var resp = $.parseJSON(response);
                    console.log('payment_response: ', resp);
                }
                catch (e) {
                    console.error('error', e)
                }
            },
            error: function (xhr, status) {
                $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                $("#stripe-pay-now-additional-btn").prop('disabled', false).removeClass('disabled');
            },
        })
            .done(function( data ) {
                $("#stripe-pay-now-btn").prop('disabled', false).removeClass('disabled');
                $("#stripe-pay-now-additional-btn").prop('disabled', false).removeClass('disabled');
            });
    },100);
}

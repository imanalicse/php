document.addEventListener('DOMContentLoaded', async () => {
    const stripe_public_key = document.querySelector("#stripe_public_key").value;
    const paymentIntent_client_secret = document.querySelector("#paymentIntent_client_secret").value;
    console.log('stripe_public_key: ', stripe_public_key)
    console.log('paymentIntent_client_secret: ', paymentIntent_client_secret)
    const stripe = Stripe(stripe_public_key);

    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                iconColor: '#666EE8',
                color: '#31325F',
                lineHeight: '40px',
                fontWeight: 300,
                fontFamily: 'Helvetica Neue',
                fontSize: '15px',
                '::placeholder': {
                    color: '#CFD7E0',
                },
            },
        }
    });
    cardElement.mount('#card-element');


    const paymentForm = document.querySelector('#payment-form');
    paymentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        paymentForm.querySelector('button').disabled = true;

        // Confirm the card payment that was created server side:
        const {error, paymentIntent} = await stripe.confirmCardPayment(
            paymentIntent_client_secret, {
                payment_method: {
                    card: cardElement,
                },
            },
        );
        if(error) {
            const error_message = error.message;
            console.log('error_message', error_message);
            console.log('error', error);
            alert(`error_message: ${error_message}`)
            paymentForm.querySelector('button').disabled = false;
            return;
        }
        if (paymentIntent.id && paymentIntent.status === "succeeded") {
            console.log('Write code here to create order')
        }
        console.log('paymentIntent', paymentIntent);
        alert(`Payment (${paymentIntent.id}): ${paymentIntent.status}`);
    });


});
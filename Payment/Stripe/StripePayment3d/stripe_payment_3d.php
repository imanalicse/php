<?php
namespace App\Payment\Stripe\StripePayment3d;

require '../../../global_config.php';

use App\Logger\Log;

$stripe_public_key = getenv('STRIPE_PUBLIC_KEY');
$stripe_secret_key = getenv('STRIPE_SECRET_KEY');

$stripe = new \Stripe\StripeClient([
    'api_key' => $stripe_secret_key,
    'stripe_version' => '2020-08-27',
]);


try {
    $paymentIntent = $stripe->paymentIntents->create([
        'payment_method_types' => ['card'],
        'amount' => 1999,
        'currency' => 'usd',
    ]);
}
catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(400);
    error_log($e->getError()->message);
    ?>
    <h1>Error</h1>
    <p>Failed to create a PaymentIntent</p>
    <p>Please check the server logs for more information</p>
    <?php
    exit;
}
catch (Exception $e) {
    error_log($e);
    http_response_code(500);
    exit;
}

?>
<link rel="stylesheet" href="style.css">
<script src="https://js.stripe.com/v3/"></script>
<script src="./stripe_custom.js"></script>
<input type="hidden" id="stripe_public_key" value="<?php echo $stripe_public_key; ?>">
<input type="hidden" id="paymentIntent_client_secret" value="<?php echo $paymentIntent->client_secret; ?>">

<div id="stripe-ui-container" class="stripe-card-wrap">
    <form id="payment-form">
        <label for="card-element">Card</label>
        <div id="card-element">
            <!-- Elements will create input elements here -->
        </div>

        <!-- We'll put the error messages in this element -->
        <div id="card-errors" role="alert"></div>

        <button id="submit">Pay</button>
    </form>
</div>

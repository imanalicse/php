<?php
namespace App\Payment\Stripe\StripePayment3d;

require '../../../global_config.php';

use App\Logger\Log;

$stripe_public_key = getenv('STRIPE_PUBLIC_KEY');
$stripe_secret_key = getenv('STRIPE_SECRET_KEY');

?>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="./stripe_payment_3.js"></script>
<input type="hidden" id="stripe_public_key" value="<?php echo $stripe_public_key; ?>">

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

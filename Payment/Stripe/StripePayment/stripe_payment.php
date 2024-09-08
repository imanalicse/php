<?php
namespace App\Payment\Stripe\StripePayment3d;

require '../../../global_config.php';

use App\Logger\Log;
$stripe_public_key = getenv('STRIPE_PUBLIC_KEY');
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="./stripe_payment.js"></script>
<input type="hidden" id="stripe_public_key" value="<?php echo $stripe_public_key; ?>">

<form action="" method="get" id="payment-form" class="mb-3 securepay-card-form" autocomplete="off">
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

    <div id="card-errors" role="alert"></div>

    <div class="button-container"><button class="btn-normal" id="stripe-pay-now-btn">Confirm & Pay</button></div>

</div>
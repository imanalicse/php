<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
  <?php
  require_once 'PayPalComponent.php';
  $paypal_client_id = \App\Payment\Paypal\StandardCheckout\PayPalComponent::getPayPalClientId();
  $seller_payer_id = '';
  ?>

  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&merchant-id=<?php echo $seller_payer_id; ?>&currency=AUD&components=buttons"></script>
    <div id="paypal-button-container"></div>
    <script src="paypal.js?v=<?php echo time() ?>"></script>
  </body>
</html>

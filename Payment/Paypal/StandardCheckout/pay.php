<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
  <?php
  require_once 'PayPalComponent.php';
  $paypal_client_id = \App\Payment\Paypal\StandardCheckout\PayPalComponent::getPayPalClientId();
  ?>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&currency=USD"></script>
    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
    <script src="paypal.js"></script>
  </body>
</html>
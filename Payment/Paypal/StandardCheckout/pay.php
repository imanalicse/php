<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
  <?php
  require '../../../global_config.php';
  use App\Payment\Paypal\StandardCheckout\PayPalComponent;

  require_once 'PayPalComponent.php';
  $paypal_compo = new PayPalComponent();
  $paypal_client_id = $paypal_compo->getPayPalClientId();
  ?>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
<!--    <script src="https://www.paypal.com/sdk/js?client-id=--><?php //echo $paypal_client_id; ?><!--&currency=AUD&disable-funding=card"></script>-->
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&currency=AUD&disable-funding=card"></script>
    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
    <script src="paypal.js?v=<?php echo time() ?>"></script>
  </body>
</html>

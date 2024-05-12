<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
  <?php
  require_once 'PayPalComponent.php';
  use App\Payment\Paypal\StandardCheckout\PayPalComponent;
  $paypal_client_id = PayPalComponent::getPayPalClientId();
  $seller_payer_id = PayPalComponent::sellerMerchantId();
  $partner_bn_code = PayPalComponent::partnerBNCode();
  ?>

  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&merchant-id=<?php echo $seller_payer_id; ?>&currency=AUD&components=buttons" data-partner-attribution-id="<?php echo $partner_bn_code; ?>"></script>
    <div id="paypal-button-container"></div>
    <script src="paypal.js?v=<?php echo time() ?>"></script>
  </body>
</html>

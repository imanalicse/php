<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
  <?php
  require_once 'PayPalMultiPartyComponent.php';
  use App\Payment\Paypal\MultipartyPayment\PayPalMultiPartyComponent;
  $paypal_client_id = PayPalMultiPartyComponent::getPayPalClientId();
  $seller_payer_id = PayPalMultiPartyComponent::getSellerPayerId();
  $partner_bn_code = PayPalMultiPartyComponent::getPartnerBNCode();
  $transaction_mode = PayPalMultiPartyComponent::getPayPalTransactionMode();
  $currency = 'AUD';
  $debug = '';
  if ($transaction_mode == 'TEST') {
      $debug = '&debug=true';
  }
  ?>
  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&merchant-id=<?php echo $seller_payer_id; ?>&currency=<?php echo $currency; ?>&disable-funding=card<?php echo $debug; ?>" data-partner-attribution-id="<?php echo $partner_bn_code; ?>" data-merchant-id="<?php echo $seller_payer_id; ?>"></script>
    <div id="paypal-button-container"></div>
    <script src="../StandardCheckout/paypal.js?v=<?php echo time() ?>"></script>
  </body>
</html>

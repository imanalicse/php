  <?php
  require_once 'PayPalMultiPartyComponent.php';

  use App\Payment\Paypal\MultipartyPayment\PayPalMultiPartyComponent;

  $paypal_compo = new PayPalMultiPartyComponent();
  $partner_merchant_id = $paypal_compo->getPayPalPartnerMerchantId();
  $tracking_id = 'imn_tracker';
  $seller_merchant_id = $paypal_compo->getPayPalSellerMerchantIdByTrackingId($partner_merchant_id, $tracking_id);
  echo '<pre>';
  echo print_r($seller_merchant_id);
  echo '</pre>';

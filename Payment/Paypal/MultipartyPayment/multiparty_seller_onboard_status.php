  <?php
  require_once 'PayPalMultiPartyComponent.php';
  use App\Payment\Paypal\StandardCheckout\PayPalComponent;

  $paypal_compo = new PayPalComponent();
  $partner_merchant_id = $paypal_compo->getPartnerPayerId();
  $seller_merchant_id = $paypal_compo->getSellerPayerId();
  $response = $paypal_compo->getSellerOnboardStatus($partner_merchant_id, $seller_merchant_id);
  echo '<pre>';
  echo print_r($response);
  echo '</pre>';

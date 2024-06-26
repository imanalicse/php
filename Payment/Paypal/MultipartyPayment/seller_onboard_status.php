  <?php
  require_once 'PayPalMultiPartyComponent.php';

  use App\Payment\Paypal\MultipartyPayment\PayPalMultiPartyComponent;

  $paypal_compo = new PayPalMultiPartyComponent();
  $partner_merchant_id = $paypal_compo->getPayPalPartnerMerchantId();
  $seller_merchant_id = $paypal_compo->getSellerPayerId();
  $response = $paypal_compo->getSellerOnboardStatus($partner_merchant_id, $seller_merchant_id);
  echo '<pre>';
  echo print_r($response);
  echo '</pre>';

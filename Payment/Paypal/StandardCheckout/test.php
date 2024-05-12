  <?php
  require_once 'PayPalComponent.php';
  use App\Payment\Paypal\StandardCheckout\PayPalComponent;

  $partner_merchant_id = '';
  $seller_merchant_id = '';
  $paypal_compo = new PayPalComponent();
  $response = $paypal_compo->getSellerOnboardStatus($partner_merchant_id, $seller_merchant_id);
  echo '<pre>';
  echo print_r($response);
  echo '</pre>';

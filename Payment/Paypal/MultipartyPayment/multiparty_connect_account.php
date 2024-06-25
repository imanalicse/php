  <?php
  require_once 'PayPalMultiPartyComponent.php';
  use App\Payment\Paypal\StandardCheckout\PayPalComponent;

  $paypal_compo = new PayPalComponent();
  $paypal_email = '';
  $response = $paypal_compo->generatePapPalConnectURL($paypal_email);
  echo '<pre>';
  echo print_r($response);
  echo '</pre>';

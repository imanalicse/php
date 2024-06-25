  <?php
  require_once 'PayPalMultiPartyComponent.php';
  use App\Payment\Paypal\MultipartyPayment\PayPalMultiPartyComponent;

  $paypal_compo = new PayPalMultiPartyComponent();
  $paypal_email = $_GET['paypal_email'];
  $response = $paypal_compo->createPartnerReferralLink($paypal_email);
  echo '<pre>';
  echo print_r($response);
  echo '</pre>';

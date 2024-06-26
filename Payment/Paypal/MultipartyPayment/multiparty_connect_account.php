  <?php
  require_once 'PayPalMultiPartyComponent.php';
  use App\Payment\Paypal\MultipartyPayment\PayPalMultiPartyComponent;

  $paypal_compo = new PayPalMultiPartyComponent();
  $paypal_email = $_GET['paypal_email'];
  $tracking_id = 'im_'.time();
  $referral_create_response = $paypal_compo->createPartnerReferralLink($tracking_id, $paypal_email);
  $action_url = '';
  if (!empty($referral_create_response)) {
      $links = $referral_create_response['links'] ?? [];
      if (!empty($links)) {
          foreach ($links as $link) {
              if ($link['rel'] == 'action_url') {
                  $action_url = $link['href'];
                  break;
              }
          }
      }
  }

  if (!empty($action_url)) {
      $action_url_part = explode('?referralToken=', $action_url);
      $partner_referral_id = $action_url_part[1];
      header('Location: '.$action_url);
//      $save_data = [
//          'organisation_id' => $organisation_id,
//          'tracking_id' => $tracking_id,
//          'paypal_email' => $paypal_email,
//          'partner_referral_id' => $partner_referral_id
//      ];
//      $saved = $this->addOrUpdatePayPalConnection($organisation_id, $save_data);
//      if (!empty($saved)) {
//          return $this->redirect($action_url);
//      }
//      else {
//          $this->Flash->adminError('Unable to save paypal connection data.', ['key' => 'admin_error']);
//      }
  }
<?php
//  require_once 'PayPalMultiPartyComponent.php';
//  use App\Payment\Paypal\StandardCheckout\PayPalComponent;
//
//  $paypal_compo = new PayPalComponent();
//  $paypal_email = '';
    paypalReturnUrl();

function paypalReturnUrl() {
    $query_data = $_GET;
    echo '<pre>';
    echo print_r($query_data);
    echo '</pre>';
    // $this->customLog('Paypal_return_url_query_data: '. $this->json_encode($query_data), 'pay_pal_connect', 'pay_pal');
//    try {
//        $tracking_id = $query_data['merchantId'] ?? ''; // organisation_uuid
//        $save_data = [];
//        $partner_merchant_id = $this->getComponent('PayPal')->getPayPalPartnerMerchantId();
//        $seller_paypal_id =  $query_data['merchantIdInPayPal'] ?? '';
//        if ($seller_paypal_id) {
//            $save_data['seller_paypal_merchant_id'] = $seller_paypal_id;
//            $onboard_status = $this->getComponent('PayPal')->getSellerOnboardStatus($partner_merchant_id, $seller_paypal_id);
//            if ($onboard_status['status']) {
//                $onboard_data = $onboard_status['data'];
//                $payments_receivable = intval($onboard_data['payments_receivable'] ?? 0);
//                $primary_email_confirmed = intval($onboard_data['primary_email_confirmed'] ?? 0);
//
//                $save_data['payments_receivable'] = $payments_receivable;
//                $save_data['primary_email_confirmed'] = $primary_email_confirmed;
//            }
//        }
//        /*
//        if (isset($query_data['isEmailConfirmed'])) {
//            $isEmailConfirmed = $query_data['isEmailConfirmed'];
//            $primary_email_confirmed = intval(boolval($isEmailConfirmed));
//            $save_data['primary_email_confirmed'] = $primary_email_confirmed;
//        }
//        */
//
//        $save_data['return_url_query_data'] = $this->json_encode($query_data);
//        $saved = $this->getComponent('PayPal')->updatePayPalConnectionByTrackingId($tracking_id, $save_data);
//
//        if ($saved) {
//            return $this->redirect(['action' => 'paypalConnected']);
//        }
//        else {
//            $this->Flash->adminError('Unable to save paypal connection data.', ['key' => 'admin_error']);
//        }
//    }
//    catch (\Exception $exception) {
//        $this->customLog('paypal paypal Return Url error: '. $exception->getMessage(), 'pay_pal_connect', 'pay_pal');
//    }
//    return $this->redirect(['action' => 'paypalConnectForm']);
}
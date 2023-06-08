<?php
namespace App\Payment\Paypal\StandardCheckout;

require '../../../global_config.php';

use App\Logger\Log;
use App\Payment\Paypal\StandardCheckout\PayPalComponent;

$paypal_compo = new PayPalComponent();
$capture_id  = $_REQUEST['capture_id'];
$url = $paypal_compo->getPayPalBaseUrl() . '/v2/payments/captures/'.$capture_id.'/refund';
try {
    $client = new \GuzzleHttp\Client();
    $access_token = $paypal_compo->generatePapPalAccessToken();
    $options = [
        'headers'=> $paypal_compo->getPayPalHeader($access_token),
        'body' => ''
    ];

    $response = $client->post($url, $options);
    $refund_response = $response->getBody()->getContents();
    $refund_response = json_decode($refund_response, true);
    Log::write('Error in Paypal refund: ', 'paypal_refund');
    Log::write($refund_response, 'paypal_refund');
}
catch (\Exception $exception) {
    Log::write('Error in Paypal refund: '. $exception->getMessage(), 'paypal_refund');
}

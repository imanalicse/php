<?php
namespace App\Payment\Paypal\StandardCheckout;

require '../../../global_config.php';

use App\Logger\Log;
use App\Payment\Paypal\StandardCheckout\PayPalComponent;

$paypal_compo = new PayPalComponent();
$post_data = $_POST;
if (isset($post_data['capture_id']) && $post_data['capture_id'] &&  isset($post_data['amount']) && $post_data['amount'] > 0) {
    echo '<pre>';
    echo print_r($post_data);
    echo '</pre>';
    $capture_id = $post_data['capture_id']; // transaction_id
    $amount = $post_data['amount'];
    $url = $paypal_compo->getPayPalBaseUrl() . '/v2/payments/captures/' . $capture_id . '/refund';
    try {
        $client = new \GuzzleHttp\Client();
        $access_token = $paypal_compo->generatePapPalAccessToken();
        $request_data = [
            'amount' => [
                'value' => $amount,
                'currency_code' => 'AUD',
            ]
        ];
        $request_data = json_encode($request_data);
        $options = [
            'headers' => $paypal_compo->getPayPalHeader($access_token),
            'body' => $request_data
        ];

        $response = $client->post($url, $options);
        $refund_response = $response->getBody()->getContents();
        $refund_response = json_decode($refund_response, true);
        Log::write('Error in Paypal refund: ', 'paypal_refund');
        Log::write($refund_response, 'paypal_refund');
        echo '<pre>';
        print_r($refund_response);
        echo '</pre>';
    } catch (\Exception $exception) {
        Log::write('Error in Paypal refund: ' . $exception->getMessage(), 'paypal_refund');
        echo '<pre>';
        echo print_r($exception->getMessage());
        echo '</pre>';
    }
}
?>
<form method="post">
    <label for="capture_id">Transaction ID</label> <input name="capture_id" id="capture_id"> <br/>
    <label for="amount">Amount</label> <input name="amount" id="amount"> <br/><br/>
    <input type="submit" value="Refund">
</form>

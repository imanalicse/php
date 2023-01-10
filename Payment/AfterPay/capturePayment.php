<?php
namespace App\Payment\AfterPay;

require '../../vendor/autoload.php';
require 'common_function.php';
use App\Logger\Log;

$after_pay_config = getAfterPayConfig();
$return_response = [
    'status' => 0,
    'message' => 'Unsupported method'
];

try {
    $post_data = $_POST;
    Log::write('capturePostData', 'after_pay');
    Log::write($post_data, 'after_pay');
    $token = $post_data['token'] ?? '';
    $merchant_order_code = $post_data['merchant_order_code'] ?? time();
    $requestParameters = [
        "token" => $token,
        "merchantReference" => $merchant_order_code
    ];
    $request_data = json_encode($requestParameters, JSON_UNESCAPED_SLASHES);

    $client = new \GuzzleHttp\Client();
    $options = [
        'headers'=>[
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'authorization' => 'Basic '. $after_pay_config['authorizationCode'],
        ],
        'body' => $request_data
    ];
    $url = $after_pay_config['ApiBaseURL'] . '/payments/capture';
    $response = $client->post($url, $options);
    if ($response->getStatusCode() == 201) {
        $payment_response_json = $response->getBody()->getContents();
        Log::write('payment_success_response', 'after_pay');
        Log::write($payment_response_json, 'after_pay');
        $payment_response = json_decode($payment_response_json, true);
        if ($payment_response['status'] === 'APPROVED') {
            // Create New order
            $return_response['status'] = 1;
            $return_response['message'] = 'Payment has been approved';
            $return_response['data'] = $payment_response;
            echo $this->json_encode($return_response);
            die();
        }
        else {
            $return_response['status'] = 0;
            $return_response['message'] = 'Payment has been declined';
            $return_response['data'] = $payment_response;
            echo $this->json_encode($return_response);
            die();
        }
    }
    else {
        Log::write('payment_error_response', 'after_pay');
        $payment_response_json = $response->getBody()->getContents();
        Log::write($payment_response_json, 'after_pay');
        $return_response['status'] = 0;
        $return_response['message'] = 'Error has been occurred';
        $return_response['data'] = json_encode($payment_response_json, true);
        echo $this->json_encode($return_response);
        die();
    }
}
catch (\Exception $exception) {

}
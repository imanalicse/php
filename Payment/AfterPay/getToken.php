<?php
namespace App\Payment\AfterPay;

require '../../vendor/autoload.php';
require 'common_function.php';

$after_pay_config = getAfterPayConfig();
$request_data = getAfterCheckoutData();
//use App\DotEnv;
use App\Logger\Log;

Log::write("getToken", 'after_pay');
try {
    $client = new \GuzzleHttp\Client();
    $options = [
        'headers'=>[
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'authorization' => 'Basic '. $after_pay_config['authorizationCode'],
        ],
        'body' => $request_data
    ];
    $url = $after_pay_config['ApiBaseURL'] . '/checkouts';
    $response = $client->post($url, $options);
//    if ($response->getStatusCode() == 201) {
//
//    }
    echo $response->getBody()->getContents();
}
catch (\Exception $exception) {
    Log::write("getTokenError", 'after_pay');
    Log::write(json_encode($exception->getMessage()), 'after_pay');
}
<?php
namespace App\Payment\AfterPay;

require '../../vendor/autoload.php';
require 'common_function.php';

$after_pay_config = getAfterPayConfig();
$request_data = getAfterCheckoutData();
echo '<pre>';
print_r($after_pay_config);
echo '</pre>';
//use App\DotEnv;
use App\Logger\Log;

Log::write("getToken", 'after_pay');

$http = new \GuzzleHttp\Client([
    'headers'=>[
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'authorization' => 'Basic '. $after_pay_config['authorizationCode'],
   ]
]);
$url = $after_pay_config['ApiBaseURL'] . '/checkouts';
$response = $http->post($url, $request_data);
echo '<pre>';
print_r($response);
echo '</pre>';
Log::write('$response', 'after_pay');
Log::write($response, 'after_pay');

echo 'Hello world';
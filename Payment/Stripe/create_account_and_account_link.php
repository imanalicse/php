<?php
namespace App\Payment\Stripe;

require '../../vendor/autoload.php';
use App\DotEnv;
use App\Logger\Log;

(new DotEnv(__DIR__ . '/.env'))->load();

$stripe = new \Stripe\StripeClient(getenv("STRIPE_SK_TEST_KEY"));
$log_file_name = "stripe_3";
$err_msg = '';
$response = '';
$link_create_response = '';
try {
 $response = $stripe->accounts->create(['type' => 'standard', "email" => "iman100@bitmascot.com"]);
 Log::write("accounts->create:Response", $log_file_name);
 Log::write($response, $log_file_name);
} catch (Exception $exception) {
    $err_msg = $exception->getMessage();
    Log::write('accounts->create:error', $log_file_name);
    Log::write($err_msg, $log_file_name);
}

if (!empty($response)) {
    try {
        $link_create_params = [
                'account' => $response->id,
                'refresh_url' => 'http://localhost/codehub/php/Payment/Stripe/refresh_url.php',
                'return_url' => 'http://localhost/codehub/php/Payment/Stripe/return_url.php',
                'type' => 'account_onboarding',
              ];

        $link_create_response = $stripe->accountLinks->create($link_create_params);
         Log::write("accountLinks->create:Response", $log_file_name);
         Log::write($link_create_response, $log_file_name);
    } catch (Exception $exception) {
        $err_msg = $exception->getMessage();
        Log::write('accountLinks->create:Error', $log_file_name);
        Log::write($err_msg, $log_file_name);
    }
}

if (!empty($link_create_response)) {
    $redirect_url = $link_create_response->url;
    header('Location: '.$redirect_url);
    die();
}
echo "<pre>";
print_r($link_create_response);
echo "</pre>";
echo "<pre>";
print_r($response);
echo "</pre>";

die("xx");
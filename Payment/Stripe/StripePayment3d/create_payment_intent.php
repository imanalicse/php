<?php
namespace App\Payment\Stripe\StripePayment3d;

require '../../../global_config.php';

use App\Logger\Log;

$stripe_secret_key = getenv('STRIPE_SECRET_KEY');

$stripe = new \Stripe\StripeClient([
    'api_key' => $stripe_secret_key,
    'stripe_version' => '2020-08-27',
]);

$response = ['status' => 'error', 'message' => ''];
$payable_amount = 5;
try {
    $paymentIntent = $stripe->paymentIntents->create([
        'payment_method_types' => ['card'],
        'amount' => $payable_amount * 100,
        'currency' => 'usd',
    ]);
    $paymentIntent_arr = json_decode(json_encode($paymentIntent, JSON_UNESCAPED_SLASHES), true);
    $response['intent_data'] = $paymentIntent_arr;
    $response['status'] = 'success';
}
catch (\Stripe\Exception\ApiErrorException $exception) {
    http_response_code(400);
    $error_message = $exception->getMessage();
    $response['message'] = 'Exception: '. $error_message;
}
catch (\Exception $exception) {
    http_response_code(500);
    $error_message = $exception->getMessage();
    $response['message'] = 'Exception: '. $error_message;
}

echo json_encode($response);
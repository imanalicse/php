<?php
namespace App\Payment\Stripe\StripePayment3d;

require '../../../global_config.php';

use App\Logger\Log;

$stripe_secret_key = getenv('STRIPE_SECRET_KEY');
$amount = 5;
$unique_id = uniqid();
$customer_id = 'cus-'. $unique_id;
$user_email = $customer_id . '@yopmail.com';
$token_info = $_POST['token_info'];

$stripe_charge_request_data = [
    // 'customer' => $customer_id,
    'source' => $token_info['id'],
    // 'receipt_email' => $user_email,
    'amount' => round($amount * 100),
    'currency' => 'AUD',
    'description'=> 'Charge for RGS Order '. $unique_id,
    'capture' => true
];

Log::write('stripe_charge_request_data: '. json_encode($stripe_charge_request_data), 'stripe', 'stripe');

$ajax_response = [
    'status' => 'error',
    'payment_data' => '',
    'message' => '',
];

try {
    \Stripe\Stripe::setApiKey($stripe_secret_key);
    $charge_response = \Stripe\Charge::create($stripe_charge_request_data);
    // $charge_response is object
    $charge_response_json = json_encode($charge_response, JSON_UNESCAPED_UNICODE);
    $charge_response_array = json_decode($charge_response_json, true);
    if ($charge_response->paid) {
        Log::write('stripe_charge_success_response: '. $charge_response_json, 'stripe', 'stripe');
        $payment_id = $charge_response['id'];
        $ajax_response['status'] = 'success';
        $ajax_response['message'] = 'Paid successfully';
        // Create order
    }
    else {
        Log::write('stripe_charge_error_response: '. $charge_response_json, 'stripe_error', 'stripe');
        $ajax_response['message'] = 'Unable to payment';
    }
    $ajax_response['payment_data'] = $charge_response_array;
}
catch (\Exception $exception) {
    $error_message = $exception->getMessage();
    $ajax_response['message'] = 'Payment exception: '. $error_message;
    Log::write('stripe_charge_exception: '. $error_message, 'stripe_error', 'stripe');
}

echo json_encode($ajax_response);



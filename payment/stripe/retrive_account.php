<?php
include "include.php";

$stripe = new \Stripe\StripeClient(getenv("STRIPE_SK_TEST_KEY"));
$log_file_name = "stripe_3";
$err_msg = '';
$account_retrieve_response = '';

try {
 $account_retrieve_response = $stripe->accounts->retrieve(
    'acct_1KTQdn2HGZgo5oHQ',
    []
 );
} catch (Exception $exception) {
    $err_msg = $exception->getMessage();
}

echo "<pre>";
print_r($err_msg);
echo "</pre>";
echo "<pre>";
print_r($account_retrieve_response);
echo "</pre>";
die("xx");
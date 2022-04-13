<?php
namespace App\Payment\Stripe;

require '../../vendor/autoload.php';
use App\DotEnv;

(new DotEnv(__DIR__ . '/.env'))->load();

/*
 * Work on express account
 */

$stripe = new \Stripe\StripeClient(getenv("STRIPE_SK_TEST_KEY"));
$response = $stripe->accounts->createLoginLink(
    getenv("STRIPE_ACCOUNT_ID")
);
echo "<pre>";
print_r($response);
echo "</pre>";
die("===EDN==");
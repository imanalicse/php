<?php
namespace App\Payment\AfterPay;

require '../../vendor/autoload.php';
use App\DotEnv;
use App\Logger\Log;

// (new DotEnv(__DIR__ . '/.env'))->load();

use Afterpay\SDK\HTTP\Request\CreateCheckout as AfterpayCreateCheckoutRequest;

$createCheckoutRequest = new AfterpayCreateCheckoutRequest([
    'amount' => [ '10.00', 'AUD' ],
    'consumer' => [
        'phoneNumber' => '0400 000 000',
        'givenNames' => 'Test',
        'surname' => 'Test',
        // 'email' => 'test@example.com'
    ],
    'billing' => [
        'name' => 'Joe Consumer',
        'line1' => 'Level 5',
        'line2' => '406 Collins Street',
        'area1' => 'Melbourne',
        'region' => 'VIC',
        'postcode' => '3000',
        'countryCode' => 'AU',
        'phoneNumber' => '0400 000 000'
    ],
    'shipping' => [
        'name' => 'Joe Consumer',
        'line1' => 'Level 5',
        'line2' => '406 Collins Street',
        'area1' => 'Melbourne',
        'region' => 'VIC',
        'postcode' => '3000',
        'countryCode' => 'AU',
        'phoneNumber' => '0400 000 000'
    ],
    'courier' => [
        'shippedAt' => '2019-01-01T00:00:00+10:00',
        'name' => 'Australia Post',
        'tracking' => 'AA0000000000000',
        'priority' => 'STANDARD'
    ],
    'items' => [
        [
            'name' => 'T-Shirt - Blue - Size M',
            'sku' => 'TSH0001B1MED',
            'quantity' => 10,
            'pageUrl' => 'https://iman.daybud.com/',
            'imageUrl' => 'https://iman.daybud.com/img/images/page-header.jpg',
            'price' => [ '10.00', 'AUD' ],
            'categories' => [
                [ 'Clothing', 'T-Shirts', 'Under $25' ],
                [ 'Sale', 'Clothing' ]
            ]
        ]
    ],
    'discounts' => [
        [
            'displayName' => '20% off SALE',
            'amount' => [ '24.00', 'AUD' ]
        ]
    ],
    'merchant' => [
        'redirectConfirmUrl' => 'http://phphub.com/Payment/AfterPay/redirectConfirmUrl.php',
        'redirectCancelUrl' => 'http://phphub.com/Payment/AfterPay/redirectCancelUrl.php'
    ],
    'taxAmount' => [ '0.00', 'AUD' ],
    'shippingAmount' => [ '0.00', 'AUD' ]
]);

$send = $createCheckoutRequest->send();
echo "<pre>";
print_r($send);
echo "</pre>";

$rawLog = $createCheckoutRequest->getRawLog();
echo "<pre>";
print_r($rawLog);
echo "</pre>";
//echo "<pre>";
//print_r($createCheckoutRequest->getParsedBody());
//echo "</pre>";
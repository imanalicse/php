<?php

const AFTER_PAY_MERCHANT_ID_TEST = '';
const AFTER_PAY_SECRET_KEY_TEST = '';

function getAfterPayConfig(): array {
     $transaction_mode = "TEST";
//    $merchant_id = Configure::Read('AFTER_PAY_MERCHANT_ID_'. $transaction_mode);
//    $secret_key = Configure::Read('AFTER_PAY_SECRET_KEY_'. $transaction_mode);

    $merchant_id = AFTER_PAY_MERCHANT_ID_TEST;
    $secret_key = AFTER_PAY_SECRET_KEY_TEST;
    $auth_code = $merchant_id . ':' . $secret_key;
    $authorizationCode = base64_encode($auth_code);

    $ApiBaseURL = 'https://global-api.afterpay.com/v2';
    if ($transaction_mode === 'TEST') {
        $ApiBaseURL = 'https://global-api-sandbox.afterpay.com/v2';
    }
    return [
        'ApiBaseURL' => $ApiBaseURL,
        'authorizationCode' => $authorizationCode
    ];
}

function getAfterCheckoutData() {
    $request_data = [
        'amount' => [ '10.00', 'AUD' ],
        'consumer' => [
            'phoneNumber' => '0400 000 000',
            'givenNames' => 'Iman',
            'surname' => 'Ali'
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
    ];

    return $request_data;
}
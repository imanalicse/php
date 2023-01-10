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

function paymentAmount() {
    return 2.00;
}

function getAfterCheckoutData() {
    $request_data = [
        'amount' => [
            'amount'=> paymentAmount(),
            'currency' => 'AUD'
        ],
        'consumer' => [
            'phoneNumber' => '0400 000 000',
            'givenNames' => 'Hello',
            'surname' => 'Test'
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
        'merchant' => [
            'redirectConfirmUrl' => 'http://phphub.com/Payment/AfterPay/redirectConfirmUrl.php',
            'redirectCancelUrl' => 'http://phphub.com/Payment/AfterPay/redirectCancelUrl.php'
        ]
    ];
    $request_data = json_encode($request_data, JSON_UNESCAPED_SLASHES);
    return $request_data;
}
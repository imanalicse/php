<?php
require '../../global_config.php';
$post_data = $_POST;
$singleUseTokenId = $post_data['token'] ?? '';

$request_data = [];
$request_data['grant_type'] = 'client_credentials';
$api_base_url = 'https://api.quickstream.support.qvalent.com/rest/v1';
$url = $api_base_url . '/transactions';

$request_data = [
    'transactionType' => 'PAYMENT',
    'singleUseTokenId' => $singleUseTokenId,
    'supplierBusinessCode' => 'C01397',
    'principalAmount' => 10.00,
    "currency" => "AUD",
    "eci" => "INTERNET",
    "ipAddress" => "127.0.0.1",
    "paymentReferenceNumber" => 'ref-'.time(),
];
echo '<pre>';
echo print_r($request_data);
echo '</pre>';
$request_data = json_encode($request_data);

try {
    $http_client = new \GuzzleHttp\Client();
    $secret_key = '';
    $auth_code = $secret_key . ':' . '';
    $authorization_code = base64_encode($auth_code);

    $headers = [
        'Content-Type' => 'application/json',
        'authorization' => 'Basic '. $authorization_code,
    ];
    $options = [
        'headers'=> $headers,
        'body' => $request_data
    ];
    $response = $http_client->post($url, $options);
    $response_data = $response->getBody()->getContents();
    $response_data = json_decode($response_data, true);
    $status_code = $response->getStatusCode();
    echo '<pre>';
    echo print_r($response_data);
    echo '</pre>';
    echo '$status_code: '. $status_code;
}
catch (\Exception $exception) {
    echo '<pre>';
    echo print_r('exception: '. $exception->getMessage());
    echo '</pre>';
}

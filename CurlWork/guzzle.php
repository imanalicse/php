<?php
namespace App\CurlWork;
require '../vendor/autoload.php';

use App\Logger\Log;

try {
    $request_data = ["username" => "iman"];
    $request_data = json_encode($request_data, JSON_UNESCAPED_SLASHES);
    $client = new \GuzzleHttp\Client();
    $options = [
        'headers'=>[
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
        'body' => $request_data
    ];
    $url = 'http://stage-image-api.com/api/v1/testOrder';
    $response = $client->post($url, $options);
    echo "hello";
    echo "<pre>";
    print_r($response);
    echo "</pre>";
    echo "<pre>";
    print_r($response->getBody()->getContents());
    echo "</pre>";

   //  echo $response->getBody()->getContents();
}
catch (\Exception $exception) {
   echo 'exception';
   echo "<pre>";
   print_r($exception->getCode());
   echo "</pre>";
    echo "<pre>";
    print_r($exception->getMessage());
    echo "</pre>";
}

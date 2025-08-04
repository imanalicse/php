<?php
require "ElasticsearchClient.php";

$params = [
    'index' => 'students',
    'id'    => 1,
    'body'  => [
        'first_name' => 'Iman',
        'last_name'  => 'Ali',
        'course'     => 'Lar'
    ]
];

$response = $client->index($params);
$status_code = $response->getStatusCode();
echo '<pre>';
print_r($response);
echo '</pre>';
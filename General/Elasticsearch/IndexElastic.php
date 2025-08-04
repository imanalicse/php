<?php
require "ElasticsearchClient.php";

$params = [
    'index' => 'students',
    'id'    => 1,
    'body'  => [
        'first_name' => 'Iman',
        'last_name'  => 'Ali',
        'course'     => 'PHP'
    ]
];

$response = $client->index($params);
echo '<pre>';
print_r($response);
echo '</pre>';
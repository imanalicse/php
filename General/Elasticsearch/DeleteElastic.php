<?php
require "ElasticsearchClient.php";

$params = [
    'index' => 'students',
    'id'    => 1
];

$response = $client->delete($params);
echo '<pre>';
print_r($response);
echo '</pre>';
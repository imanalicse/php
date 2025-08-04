<?php
require "ElasticsearchClient.php";

$params = [
    'index' => 'students',
    'id'    => 1,
    'body'  => [
        'doc' => [
            'course' => 'Laravel'
        ]
    ]
];

$response = $client->update($params);
echo '<pre>';
print_r($response);
echo '</pre>';
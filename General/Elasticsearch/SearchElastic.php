<?php
require "ElasticsearchClient.php";

$params = [
    'index' => 'students',
    'body'  => [
        'query' => [
            'match' => [
                'course' => 'PHP'
            ]
        ]
    ]
];

$response = $client->search($params);
$status_code = $response->getStatusCode();
$search_data = $response['hits']['hits'];
echo '<pre>';
print_r($search_data);
echo '</pre>';
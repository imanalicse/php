<?php
require "ElasticsearchClient.php";

$params = [
    'index' => 'students',
    'body'  => [
        'size' => 1000, // max number of documents to return
        'query' => [
            'match_all' => new \stdClass() // or simply []
        ]
    ]
];

$response = $client->search($params);
$status_code = $response->getStatusCode();
$search_data = $response['hits']['hits'];
echo '<pre>';
print_r($search_data);
echo '</pre>';
if (!empty($search_data)) {
    foreach ($search_data as $hit) {
        $actual_data = $hit['_source']; // this is your actual data
        echo '<pre>';
        print_r($actual_data);
        echo '</pre>';
    }
}
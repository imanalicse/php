<?php
require "ElasticsearchClient.php";

$response = $client->cat()->indices(['format' => 'json']);
$data = $response->asArray();
foreach ($data as $index) {
    echo $index['index'] . "\n";
}
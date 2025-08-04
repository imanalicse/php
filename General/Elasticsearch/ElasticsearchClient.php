<?php
require './../../global_config.php';

use Elastic\Elasticsearch\ClientBuilder;

$host = getenv('ELASTICSEARCH_HOST'); // https://localhost:9200
$username = getenv('ELASTICSEARCH_USERNAME');
$password = getenv('ELASTICSEARCH_PASSWORD');

$client = ClientBuilder::create()
    ->setHosts([$host]) // or Elastic Cloud URL
    ->setBasicAuthentication($username, $password)
    ->setSSLVerification(false) // for local
    ->build();

$response = $client->ping();

if ($response) {
    echo "Elasticsearch is up!";
}
else {
    echo "Connection failed.";
}

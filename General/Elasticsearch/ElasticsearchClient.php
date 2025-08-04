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

$ping_response = $client->ping();

if (!$ping_response) {
    die("Elasticsearch is down!");
}

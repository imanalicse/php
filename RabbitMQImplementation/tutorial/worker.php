<?php
require_once __DIR__ . './../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'iman', 'iman');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

 // distribute message fairly
$channel->basic_qos(null, 1, null);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    // sleep(3);
    $channel->wait();
}

$channel->close();
$connection->close();
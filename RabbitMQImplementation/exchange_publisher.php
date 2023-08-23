<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once 'rabbitmq_config.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$exchange = 'image';
$event_name = 'processImage';

$connection = new AMQPStreamConnection(
    RABBITMQ_HOST,
    RABBITMQ_PORT,
    RABBITMQ_USERNAME,
    RABBITMQ_PASSWORD
);

$channel = $connection->channel();
$routing_key = $exchange. '.' .$event_name;
$channel->exchange_declare($exchange, 'topic', false, true, false);
echo $image_name = $argv[1];
$message = [
    'image_name' => $image_name,
    'time' => date("Y-m-d H:i:s"),
];
$data = json_encode($message, JSON_UNESCAPED_SLASHES);
$msg = new AMQPMessage(
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT) # make message persistent
);

$channel->basic_publish($msg, $exchange, $routing_key);

$channel->close();
$connection->close();

// php publisher_exchange.php S221231AN201-2.jpg
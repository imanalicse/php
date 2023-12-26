<?php
namespace App\RabbitMQImplementation;

use App\RabbitMQImplementation\DefaultDirectPublisher;

$message = [
    'title' => 'Hello message '. time(),
    'publish_at' => date("Y-m-d H:i:s"),
];
$exchange_name = 'topic_logs';
$binding_pattern = 'user.created';
$routingKey1 = 'user.created';
$routingKey2 = 'order.updated';
DefaultDirectPublisher::publish($message, $exchange_name, $binding_pattern);
echo "hello";
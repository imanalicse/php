<?php
namespace App\RabbitMQImplementation\DefaultDirect;

require_once(__DIR__ . '/../../vendor/autoload.php');

use App\RabbitMQImplementation\DefaultDirect\DefaultDirectPublisher;

$message = [
    'title' => 'notification message '. time(),
    'publish_at' => date("Y-m-d H:i:s"),
];
$routingKey = 'notification';
DefaultDirectPublisher::publish($message, $routingKey);
echo "notification";

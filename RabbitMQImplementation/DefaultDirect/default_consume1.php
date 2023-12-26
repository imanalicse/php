<?php
namespace App\RabbitMQImplementation\DefaultDirect;

require_once(__DIR__ . '/../../vendor/autoload.php');

use App\RabbitMQImplementation\DefaultDirect\DefaultDirectConsumer;

DefaultDirectConsumer::listenRabbitMQ('notificationGroup',  function ($msg) {
    echo $msg->body .  "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
});


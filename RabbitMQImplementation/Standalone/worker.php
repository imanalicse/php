<?php
require_once __DIR__ . './../../vendor/autoload.php';
require_once __DIR__ . '/../rabbitmq_config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

define("RABBITMQ_QUEUE_NAME", "task_queue");

$connection = new AMQPStreamConnection(
    RABBITMQ_HOST,
    RABBITMQ_PORT,
    RABBITMQ_USERNAME,
    RABBITMQ_PASSWORD
);


$channel = $connection->channel();

/**
 * we declare the queue here, as well. Because we might start the consumer before the publisher,
 * we want to make sure the queue exists before we try to consume messages from it.
*/
# Create the queue if it doesnt already exist.
$channel->queue_declare(RABBITMQ_QUEUE_NAME, false, true, false, false, false,
    null, null
);


echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n";
    $job = json_decode($msg->body, $assocForm=true);
    sleep($job['sleep_period']);
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

// distribute message fairly
$channel->basic_qos(null, 1, null);

 // start consuming
$channel->basic_consume(RABBITMQ_QUEUE_NAME, '', false, false, false, false,
    $callback
);

// while (count($channel->callbacks))
// while ($channel->is_consuming())
while ($channel->is_open())
{
    $channel->wait();
}

$channel->close();
$connection->close();
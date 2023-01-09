<?php
require_once __DIR__ . './../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// $channel->queue_declare('hello', false, false, false, false);
// task_queue queue won't be lost even if RabbitMQ restarts by setting durable as true
$channel->queue_declare('task_queue', false, true, false, false);


echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
};

// Fair dispatch:  distribute message fairly
/**
 * ->basic_qos method with the prefetch_count = 1 setting tells RabbitMQ not to give more than one message to a worker at a time.
 * Or, in other words, don't dispatch a new message to a worker until it has processed and acknowledged
 * the previous one. Instead, it will dispatch it to the next worker that is not still busy.
 */
$channel->basic_qos(null, 1, null);

// $channel->basic_consume('hello', '', false, true, false, false, $callback);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    // sleep(3);
    $channel->wait();
}

$channel->close();
$connection->close();
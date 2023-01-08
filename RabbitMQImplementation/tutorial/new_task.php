<?php
require_once __DIR__ . './../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * http://localhost:15672
 */
$connection = new AMQPStreamConnection('localhost', 5672, 'iman', 'iman');
$channel = $connection->channel();

/**
 * To send, we must declare a queue for us to send to; then we can publish a message to the queue:
 * Declaring a queue is idempotent - it will only be created if it doesn't exist already.
 */
$channel->queue_declare('hello', false, false, false, false);

// The message content is a byte array, so you can encode whatever you like there.

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "Hello World!";
}
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, '', 'hello');


$channel->close();
$connection->close();
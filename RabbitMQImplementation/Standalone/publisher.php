<?php

use PhpAmqpLib\Message\AMQPMessage;

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once __DIR__ . '/../rabbitmq_config.php';

define("RABBITMQ_QUEUE_NAME", "task_queue");

$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
    RABBITMQ_HOST,
    RABBITMQ_PORT,
    RABBITMQ_USERNAME,
    RABBITMQ_PASSWORD
);

$channel = $connection->channel();

# Create the queue if it does not already exist.
$channel->queue_declare(RABBITMQ_QUEUE_NAME, false, true, false, false, false,
    null,
    null
);

$job_id=0;
while (true)
{
    $jobArray = array(
        'id' => $job_id++,
        'task' => 'sleep',
        'sleep_period' => rand(0, 3)
    );

    $msg = new \PhpAmqpLib\Message\AMQPMessage(
        json_encode($jobArray, JSON_UNESCAPED_SLASHES),
        array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT) # make message persistent
    );

    $channel->basic_publish($msg, '', RABBITMQ_QUEUE_NAME);
    print 'Job created' . PHP_EOL;
    sleep(1);
}

$channel->close();
$connection->close();
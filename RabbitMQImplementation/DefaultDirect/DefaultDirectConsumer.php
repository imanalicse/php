<?php
namespace App\RabbitMQImplementation\DefaultDirect;
require_once(__DIR__ . '/../../vendor/autoload.php');
use App\RabbitMQImplementation\Connection;
use PhpAmqpLib\Message\AMQPMessage;

class DefaultDirectConsumer {

    public static function listenRabbitMQ($binding_key, $callback) {
        $queue_name = 'rgs.' . $binding_key;
        echo $binding_key . ' started listener'. "\n";
        $connection = Connection::instance();
        $channel = $connection->channel();

        # Create the queue if it doesn't already exist.
        $channel->queue_declare($queue_name, false, true, false, false);
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queue_name, '', false, false, false,
            false, function($msg) use($callback) {
                $callback($msg);
            }
        );

        while (count($channel->callbacks))
        {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
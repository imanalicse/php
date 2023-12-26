<?php
namespace App\RabbitMQImplementation\DefaultDirect;

use PhpAmqpLib\Message\AMQPMessage;
use App\RabbitMQImplementation\Connection;

class DefaultDirectPublisher {

    public static function publish($message, $routing_key): void {
        self::publish_message($message, $routing_key);
    }

    private static function publish_message($message, $routing_key) {
        $data = json_encode($message, JSON_UNESCAPED_SLASHES);
        if (!isset($data)) {
            throw new \Exception("Unpublishable message");
        }
        $msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

        if (empty($routing_key)) {
            throw new \Exception("Invalid routing key");
        }
        $routing_key_final = 'rgs.'.$routing_key;
        $connection = Connection::instance();
        $channel = $connection->channel();
        $channel->queue_declare($routing_key_final, false, true, false, false);
        $channel->basic_publish($msg, '', $routing_key_final);
    }
}
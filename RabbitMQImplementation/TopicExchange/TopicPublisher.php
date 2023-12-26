<?php
namespace App\RabbitMQImplementation;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once __DIR__ . '/../rabbitmq_config.php';

class TopicPublisher {

    private function __construct() {  }

    protected static $instance = null;

    public static function instance(): DefaultDirectPublisher {
        if (is_null(self::$instance)) {
            self::$instance = new DefaultDirectPublisher();
        }
        return self::$instance;
    }

    public static function publish($message, $exchange_name, $routing_key): void {
        $publisher = static::instance();
        $publisher->publish_message($message, $exchange_name, $routing_key);
    }

    /**
     * @throws \Exception
     */
    private function publish_message($message, $exchange, $routing_key) {
        $data = json_encode($message, JSON_UNESCAPED_SLASHES);
        if (!isset($data)) {
            throw new \Exception("Unpublishable message");
        }

        $msg = new AMQPMessage(
            $data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT) # make message persistent
        );

        if (!isset($exchange)) {
            throw new \Exception("Invalid exchange");
        }

        $connection = Connection::instance();
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'topic', false, true, false);

        // $channel->queue_declare($routing_key, false, true, false, false);

        $channel->basic_publish($msg, $exchange, $routing_key);
    }
}
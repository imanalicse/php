<?php
namespace App\RabbitMQImplementation;

use PhpAmqpLib\Connection\AMQPStreamConnection;
require_once __DIR__ . '/rabbitmq_config.php';

class Connection {
    protected static $instance = null;

    public static function instance() : AMQPStreamConnection {
        if (is_null(self::$instance)) {
            self::$instance = new AMQPStreamConnection(
                RABBITMQ_HOST,
                RABBITMQ_PORT,
                RABBITMQ_USERNAME,
                RABBITMQ_PASSWORD
            );
        }
        return self::$instance;
    }
}


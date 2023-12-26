<?php
namespace App\RabbitMQImplementation\DefaultDirect;
require_once(__DIR__ . '/../../vendor/autoload.php');
use App\RabbitMQImplementation\Connection;
use PhpAmqpLib\Message\AMQPMessage;

class DefaultDirectConsumerMultiple {
    private $channel;
    protected static $instance = null;
    private $listeners = [];
    private $instance_name;


    private function __construct($instance_name) {
        $connection = Connection::instance();
        $this->channel = $connection->channel();
        $this->instance_name = $instance_name;
    }

    public static function instance($instance_name): DefaultDirectConsumerMultiple {
        if (is_null(self::$instance)) {
            self::$instance = new DefaultDirectConsumerMultiple($instance_name);
        }
        return self::$instance;
    }

    public function listen_to($binding_key, $callback) {
        $consume_data  = [
            'binding_key' => $binding_key,
            'callback' => $callback
        ];
        array_push($this->listeners, $consume_data);
    }


    public function start_consumer() {
        if (empty($this->listeners)) {
            echo 'No listener is registered yet. Closing channel...\r\n';
            $this->channel->close();
            exit();
        }
        if (!isset($this->instance_name)) {
            echo 'empty instance detected. Please provide an instance name.\r\n';
            $this->channel->close();
            exit();
        }

        foreach ($this->listeners as $listener) {
            $binding_key = $listener['binding_key'];
            $callback = $listener['callback'];
            // create a queue name consisting of binding key
            // $queue_name = 'rgs.' . $binding_key;
            $queue_name = $this->instance_name .'.' . $binding_key;
            $this->channel->queue_declare($queue_name, false, true, false, false);
            $this->channel->basic_qos(null, 1, null);
            $this->channel->basic_consume($queue_name, '', false, false, false,
                false, function($msg) use($callback) {
                    $callback($msg);
                }
            );

            while ($this->channel->is_consuming())
            {
                $this->channel->wait();
            }

            // $this->channel->close();
            // $this->connection->close();
        }
    }

    public function stop_consumer() {
        if (isset($this->channel) && $this->channel->is_open()) {
            $this->channel->close();
        }
    }

    public function __destruct() {
        $this->stop_consumer();
    }
}

$consumer = DefaultDirectConsumerMultiple::instance("rgs");
$consumer->listen_to('notificationGroup', function ($msg) {
    echo $msg->body .  "\n";
    // $msg->ack();
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
});
$consumer->start_consumer();

$consumer = DefaultDirectConsumerMultiple::instance("rgs");
$consumer->listen_to('notification', function ($msg) {
    echo $msg->body .  "\n";
    // $msg->ack();
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
});
$consumer->start_consumer();
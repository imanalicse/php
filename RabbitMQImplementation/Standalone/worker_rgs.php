<?php
require_once __DIR__ . './../../vendor/autoload.php';
require_once __DIR__ . '/../rabbitmq_config.php';

const LIONO_SITE = 'http://stage-image-api.com/';


function remoteRequest($url, $msg){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($msg));

    // In real life you should use something like:
    // curl_setopt($ch, CURLOPT_POSTFIELDS,
    //http_build_query(array('postvar1' => 'value1')));

    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    echo 'CURL_Response::'.$url.PHP_EOL.'+++++['.print_r($server_output,true).']++++++';

    curl_close ($ch);
}

$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
    RABBITMQ_HOST,
    RABBITMQ_PORT,
    RABBITMQ_USERNAME,
    RABBITMQ_PASSWORD
);


$channel = $connection->channel();

# Create the queue if it doesnt already exist.
$channel->queue_declare(
    $queue = 'processImage',
    $passive = false,
    $durable = true,
    $exclusive = false,
    $auto_delete = false,
    $nowait = false,
    $arguments = null,
    $ticket = null
);


echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n";
    $message_body = json_decode($msg->body, $assocForm=true);
//    echo "<pre>";
//    print_r($message_body);
//    echo "</pre>";
    $url = rtrim(LIONO_SITE, '/') . '/' . "api/v1/rabbitMQImage";
    remoteRequest($url, $msg);

    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

// $channel->exchange_declare('EBemail', 'topic', false, true, false);

$channel->basic_qos(null, 1, null);

$channel->basic_consume(
    $queue = "processImage",
    $consumer_tag = '',
    $no_local = false,
    $no_ack = false,
    $exclusive = false,
    $nowait = false,
    $callback
);

while (count($channel->callbacks))
{
    $channel->wait();
}

$channel->close();
$connection->close();

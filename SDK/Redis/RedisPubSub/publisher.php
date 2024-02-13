<?php

$redis = new Redis();
$redis->pconnect('127.0.0.1', 6379);

$channel = 'my_channel';
$message = 'Hello, Redis Pub/Sub!';

$redis->publish($channel, $message);

echo "Message published to '$channel': $message\n";

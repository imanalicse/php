<?php

$redis = new Redis();
$redis->pconnect('127.0.0.1', 6379);

$channel = 'my_channel';

// Subscribe to the channel
$redis->subscribe([$channel], function ($redis, $channel, $message) {
    echo "Received message from channel '$channel': $message\n";
});

// Keep the script running to continue listening
while (true) {
    $redis->ping();
    usleep(10000); // Sleep for 10 milliseconds
}

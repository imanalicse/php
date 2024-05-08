<?php
// Connecting to Redis server on localhost
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
echo "Connection to server sucessfully";

// Check whether server is running or not
echo "Server is running: " . $redis->ping();

// Set and get cache data
$redis->set('test_key', 'Hello Redis');
$value = $redis->get('test_key');
echo $value;  // Outputs 'Hello Redis'

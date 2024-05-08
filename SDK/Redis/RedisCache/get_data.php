<?php
$redis = new Redis();
// $redis->pconnect('127.0.0.1', 6379);
echo '<pre>';
echo print_r($redis->getPort());
echo '</pre>';
echo '<pre>';
echo print_r(get_class_methods($redis));
echo '</pre>';
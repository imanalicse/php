<?php

require __DIR__ . '/../vendor/autoload.php';

use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

$start_time = microtime(true);
$responses = wait(parallelMap([
    'https://google.com/',
    'https://github.com/',
    'https://stackoverflow.com/',
], function ($url) {
    return $url;
}));

function helloFn($url) {
//    echo "<pre>";
//    print_r($url);
//    echo "</pre>";
    return $url;
}

$end_time = microtime(true);
echo "<pre>";
print_r('Parallel Time Taken:'. ($end_time - $start_time));
echo "</pre>";
echo "<pre>";
print_r($responses);
echo "</pre>";
<?php


require __DIR__ . '/../vendor/autoload.php';

use Amp\Parallel\Worker;
use Amp\Promise;

function testFunc(): string
{
    sleep(1);
    return 'abc';
}

$start_time = microtime(true);
$urls = [
    'https://secure.php.net',
    'https://amphp.org',
    'https://github.com',
];

$promises = [];
foreach ($urls as $url) {
    // $promises[$url] = Worker\enqueueCallable('file_get_contents', $url);
    $promises[$url] = Worker\enqueueCallable('testFunc', $url);
}
$responses = Promise\wait(Promise\all($promises));

foreach ($responses as $url => $response) {
echo "<pre>";
print_r(strlen($response) . '->' . $url);
echo "</pre>";

}
$end_time = microtime(true);
echo "<pre>";
print_r('Parallel Time Taken:'. ($end_time - $start_time));
echo "</pre>";
echo "========================";
$start_time2 = microtime(true);
$contents = [];
foreach ($urls as $url) {
    $contents[$url] = file_get_contents($url);
}

foreach ($contents as $url => $response) {
    echo "<pre>";
    print_r(strlen($response) . '->' . $url);
    echo "</pre>";
}
$end_time2 = microtime(true);
echo "<pre>";
print_r('Serial Taken:'. ($end_time2 - $start_time2));
echo "</pre>";
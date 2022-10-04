<?php


require __DIR__ . '/../vendor/autoload.php';

use Amp\Loop;

function tick() {
    echo "tick<br>";
}

echo "-- before Loop::run()<br>";

Loop::run(function() {
    echo "Hello run<br>";
    tick();
    // Loop::repeat($msInterval = 1000, "tick");
    // Loop::delay($msDelay = 7000, "Amp\\Loop::stop");
});

echo "-- after Loop::run()<br>";
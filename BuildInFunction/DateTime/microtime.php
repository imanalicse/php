<?php
/**
 * microtime — Return current Unix timestamp with microseconds
 * microtime(bool $as_float = false): string|float
 * as_float: If used and set to true, microtime() will return a float instead of a string
 * Return Values:
 *  By default, microtime() returns a string in the form "msec sec", where sec is the number of seconds since the
 * Unix epoch (0:00:00 January 1,1970 GMT), and msec measures microseconds that have elapsed since sec and is also
 * expressed in seconds as a decimal fraction.
 *
 * If as_float is set to true, then microtime() returns a float, which represents the current time in seconds
 * since the Unix epoch accurate to the nearest microsecond.
 */


$time_start = microtime(true);
// Sleep for a while
usleep(100);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Did nothing in $time seconds\n";
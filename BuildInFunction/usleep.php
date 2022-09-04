<?php
/**
 * usleep — Delay execution in microseconds
 * usleep(int $microseconds): void
 */
 // Current time
echo date('h:i:s') . "<br>";
// wait for 2 seconds
usleep(2000000);
// back!
echo date('h:i:s') . "<br>";
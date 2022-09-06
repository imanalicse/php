<?php
/**
 * range — Create an array containing a range of elements
 * range(string|int|float $start, string|int|float $end, int|float $step = 1): array
 */
$ranges = range(0, 12, 2);
echo "<pre>";
print_r($ranges);
echo "</pre>";
$ranges = range('a', 'i');
echo "<pre>";
print_r($ranges);
echo "</pre>";

echo "<pre>";
print_r(range('c', 'a'));
echo "</pre>";
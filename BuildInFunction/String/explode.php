<?php
/**
 * explode — Split a string by a string
 * explode(string $separator, string $string, int $limit = PHP_INT_MAX): array
 */

$pizza  = "piece1 piece2 piece3 piece4 piece5 piece6";
$pieces = explode(" ", $pizza);
echo "<pre>";
print_r($pieces);
echo "</pre>";

$str = 'one|two|three|four';
// positive limit
print_r(explode('|', $str, 2));
// negative limit
print_r(explode('|', $str, -1));

/*
Array
(
    [0] => one
    [1] => two|three|four
)
Array
(
    [0] => one
    [1] => two
    [2] => three
)
*/
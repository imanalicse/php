<?php
/**
 * preg_split — Split string by a regular expression
 * preg_split(
    string $pattern,
    string $subject,
    int $limit = -1,
    int $flags = 0
): array|false
 */

$keywords = preg_split("/[\s,]+/", "hypertext language, programming");
echo "<pre>";
print_r($keywords);
echo "</pre>";
/*
Array
(
    [0] => hypertext
    [1] => language
    [2] => programming
)
*/
<?php
/**
 * file_get_contents — Reads entire file into a string
 * file_get_contents(
    string $filename,
    bool $use_include_path = false,
    ?resource $context = null,
    int $offset = 0,
    ?int $length = null
): string|false
 * This function is similar to file(), except that file_get_contents() returns the file in a string,
 starting at the specified offset up to length bytes. On failure, file_get_contents() will return false.
 */

$file = file_get_contents('somefile.txt');
echo "<pre>";
print_r($file);
echo "</pre>";

// Read 14 characters starting from the 21st character
$section = file_get_contents('somefile.txt', FALSE, NULL, 20, 14);
var_dump($section);
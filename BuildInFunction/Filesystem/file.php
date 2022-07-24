<?php
/**
 * file — Reads entire file into an array
 * file(string $filename, int $flags = 0, ?resource $context = null): array|false
 * You can use file_get_contents() to return the contents of a file as a string.
 */

$lines = file('somefile.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
echo "<pre>";
print_r($lines);
echo "</pre>";
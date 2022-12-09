<?php
/**
 * is_file — Tells whether the filename is a regular file
 * is_file(string $filename): bool
 */

// $filename = 'a_file.txt';
$filename = __FILE__;
echo "<pre>";
print_r($filename);
echo "</pre>";
echo "<pre>";
print_r(var_dump(is_file($filename)));
echo "</pre>";
echo '=======';
$directory = dirname($filename);
echo "<pre>";
print_r($directory);
echo "</pre>";
echo "<pre>";
print_r(var_dump(is_file($directory)));
echo "</pre>";
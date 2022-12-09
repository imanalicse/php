<?php
/**
 * basename — Returns trailing name component of path
 *  basename(string $path, string $suffix = ""): string
 */

$path = __FILE__;
$path = __DIR__;
echo "<pre>";
print_r($path);
echo "</pre>";

echo basename($path) .'<br>';
echo basename($path, '.php');
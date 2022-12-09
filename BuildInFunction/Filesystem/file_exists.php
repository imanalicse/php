<?php
/**
 * file_exists — Checks whether a file or directory exists
 * file_exists(string $filename): bool
 */

$filename = __FILE__;
if (file_exists($filename)) {
    echo "The file '$filename' exists";
}
else {
    echo "The file '$filename' does not exist";
}
echo '<br/>';
$directory = dirname(__FILE__, 3);
if (file_exists($directory)) {
    echo "The directory '$directory' exists";
}
else {
    echo "The directory '$directory' does not exist";
}
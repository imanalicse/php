<?php
/**
 * file_exists — Checks whether a file or directory exists
 * file_exists(string $filename): bool
 */

$filename = '/path/to/foo.txt';
if (file_exists($filename)) {
    echo "The file $filename exists";
} else {
    echo "The file $filename does not exist";
}
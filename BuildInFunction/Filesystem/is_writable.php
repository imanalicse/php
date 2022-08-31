<?php
/**
 * is_writable — Tells whether the filename is writable
 *  is_writable(string $filename): bool
 */

$filename = 'somefile.txt';
$filename = 'D:/wamp/www/image_process/southam/incoming/MappedImages/S221231UC016-20.jpg';
$filename = 'D:/wamp/www/image_process/southam/incoming/MappedImages/';
if (is_writable($filename)) {
    echo 'The file is writable';
} else {
    echo 'The file is not writable';
}
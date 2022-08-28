<?php
/**
 * copy — Copies file
 * copy(string $from, string $to, ?resource $context = null): bool
 */

$file = 'D:/wamp/www/image_process/southam/incoming/MappedImages/S221231UC016-20.jpg';
$newfile = 'S221231UC016-20.jpg';

if (!copy($file, $newfile)) {
    echo "failed to copy $file...\n";
}
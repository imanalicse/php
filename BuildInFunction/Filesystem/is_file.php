<?php
/**
 * is_file — Tells whether the filename is a regular file
 * is_file(string $filename): bool
 */

var_dump(is_file('a_file.txt')) . "\n"; // bool(true)
var_dump(is_file('/usr/bin/')) . "\n"; // bool(false)
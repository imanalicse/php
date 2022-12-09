<?php
/**
 * dirname — Returns a parent directory's path
 *  dirname(string $path, int $levels = 1): string
 *
 * path: On Windows, both slash (/) and backslash (\) are used as directory separator character.
 *In other environments, it is the forward slash (/).
 *
 * levels: The number of parent directories to go up. This must be an integer greater than 0.
 */

echo dirname(__FILE__) . "<br>";
echo dirname(__DIR__) . "<br>";
echo dirname("/etc/passwd") . "<br>";
echo dirname("/etc/") . "<br>";
echo dirname(".") . "<br>";
echo dirname("C:\\") . "<br>";
echo dirname("/usr/local/lib", 2) . '<br>';
/*
/etc
/ (or \ on Windows)
.
C:\
/usr
*/
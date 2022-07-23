<?php
/**
preg_match — Perform a regular expression match
preg_match( string $pattern, string $subject, array &$matches = null, int $flags = 0, int $offset = 0): int|false
 */

// get host name from URL
preg_match('@^(?:http://)?([^/]+)@i', "http://www.php.net/index.html", $matches);
$host = $matches[1]; // www.php.net
// get last two segments of host name
preg_match('/[^.]+\.[^.]+$/', $host, $matches);
echo "domain name is: {$matches[0]}\n"; // domain name is: php.net

// TODO
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

// Positive lookahead: match a number followed by the text lb
if (preg_match('/\d+(?=lb)/', '2 chicken weigh 30lb', $matches)) {
    echo "<pre>";
    print_r($matches);
    echo "</pre>";
}

// Negative lookahead: match a number not followed by the text lb
if (preg_match('/\d+(?!lb)/', '2 chicken weigh 30lb', $matches)) {
    echo "<pre>";
    print_r($matches);
    echo "</pre>";
}

// TODO

$file_name = 'S221231AN001-DC001.jpg';
$first_character =  substr($file_name,0,1);
$character_before_hyphen = explode('-', $file_name)[0];
$extension = pathinfo($file_name, PATHINFO_EXTENSION);
$is_valid_filename = false;
if ($first_character == 'S' && strlen($character_before_hyphen) == 12 && in_array($extension, ['jpg', 'JPG', 'jpeg', 'JPEG'])) {
    $is_valid_filename = true;
}
echo "<pre>";
print_r($is_valid_filename);
echo "</pre>";
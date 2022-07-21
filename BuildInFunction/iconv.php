<?php
/**
 * iconv — Convert a string from one character encoding to another
 * iconv(string $from_encoding, string $to_encoding, string $string): string|false
 * Return Values: Returns the converted string, or false on failure.
 */

 $text = "This is the Euro symbol '€'.";

echo 'Original : ', $text, PHP_EOL;
echo 'TRANSLIT : ', iconv("UTF-8", "ISO-8859-1//TRANSLIT", $text), PHP_EOL;
echo 'IGNORE   : ', iconv("UTF-8", "ISO-8859-1//IGNORE", $text), PHP_EOL;
echo 'Plain    : ', iconv("UTF-8", "ISO-8859-1", $text), PHP_EOL;

/*
Original : This is the Euro symbol '€'.
TRANSLIT : This is the Euro symbol 'EUR'.
IGNORE   : This is the Euro symbol ''.
Plain    :
Notice: iconv(): Detected an illegal character in input string in .\iconv-example.php on line 13
*/
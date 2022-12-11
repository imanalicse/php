<?php
/**
 * str_pad — Pad a string to a certain length with another string
 * str_pad(
    string $string,
    int $length,
    string $pad_string = " ",
    int $pad_type = STR_PAD_RIGHT
    ): string
 *
 * This function returns the string string padded on the left, the right, or both sides to the specified padding length.
 * If the optional argument pad_string is not supplied, the string is padded with spaces,
 * otherwise it is padded with characters from pad_string up to the limit.
 */
$input = "Alien";
echo str_pad($input, 10) . "<br>";                      // produces "Alien     "
echo str_pad($input, 10, "-=", STR_PAD_LEFT). "<br>";  // produces "-=-=-Alien"
echo str_pad($input, 10, "_", STR_PAD_BOTH). "<br>";   // produces "__Alien___"
echo str_pad($input,  6, "___"). "<br>";               // produces "Alien_"
echo str_pad($input,  3, "*"). "<br>";                 // produces "Alien"
<?php
/**
 implode — Join array elements with a string
 implode(string $separator, array $array): string
 */

$array = ['lastname', 'email', 'phone'];
var_dump(implode(",", $array)); // string(20) "lastname,email,phone"
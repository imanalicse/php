<?php
/**
 * https://www.php.net/manual/en/control-structures.match.php
 * Structure of a match expression
$return_value = match (subject_expression) {
    single_conditional_expression => return_expression,
    conditional_expression1, conditional_expression2 => return_expression,
};

*
 */


$food = 'cake';

$return_value = match ($food) {
    'apple' => 'This food is an apple',
    'bar' => 'This food is a bar',
    'cake' => 'This food is a cake',
};
var_dump($return_value); // string(19) "This food is a cake"
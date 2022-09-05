<?php
/**
 * Anonymous functions, also known as closures, allow the creation of functions which have no specified name.
 * They are most useful as the value of callable parameters, but they have many other uses.
 */

 // Example #2 Anonymous function variable assignment example
 $greet = function($name)
{
    printf("Hello %s<br/>", $name);
};

$greet('World');
$greet('PHP');

// Example #3 Inheriting variables from the parent scope

$message = 'hello';
// Inherit $message
$example = function () use ($message) {
    var_dump($message);
    echo '<br>';
};
$example();

// Inherited variable's value is from when the function is defined, not when called

// Closures can also accept regular arguments
$message = 'world';
$example = function ($arg) use ($message) {
    var_dump($arg . ' ' . $message);
    echo '<br>';
};
$example("hello");

// Return type declaration comes after the use clause
$example = function () use ($message): string {
    return "hello $message";
};
var_dump($example());
echo '<br>';

// TODO
<?php
/**
 * Anonymous functions, also known as closures, allow the creation of functions which have no specified name.
 * They are most useful as the value of callable parameters, but they have many other uses.
 */

#Example #1 Anonymous function example

echo preg_replace_callback('~-([a-z])~', function ($match) {
    return strtoupper($match[1]);
}, 'hello-world');
// outputs helloWorld
echo '<br/>';

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
$message2 = 'proxima';
$example = function ($arg) use ($message, $message2) {
    var_dump($arg . ' ' . $message . ' '. $message2);
    echo '<br>';
};
$example("hello");

// Return type declaration comes after the use clause
$example = function () use ($message): string {
    return "hello $message";
};
var_dump($example());
echo '<br>';

/**
 * Inheriting variables from the parent scope is not the same as using global variables.
 * Global variables exist in the global scope, which is the same no matter what function is executing.
 * The parent scope of a closure is the function in which the closure was declared (not necessarily the function
 * it was called from).
 */
 // Example #4 Closures and scoping

 // Example #5 Automatic binding of $this
 // Example #6 Attempting to use $this inside a static anonymous function

// TODO
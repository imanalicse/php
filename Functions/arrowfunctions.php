<?php
/**
 * Arrow functions have the basic form fn (argument_list) => expr.
 *
 * Arrow functions support the same features as anonymous functions,
 * except that using variables from the parent scope is always automatic.
 *
 * When a variable used in the expression is defined in the parent scope it will be implicitly captured by-value.
 * In the following example, the functions $fn1 and $fn2 behave the same way.
 */

// Example #1 Arrow functions capture variables by value automatically
$y = 1;
$fn1 = fn($x) => $x + $y;
// equivalent to using $y by value:
$fn2 = function ($x) use ($y) {
    return $x + $y;
};
var_export($fn1(3));
echo '<br>';

// Example #2 Arrow functions capture variables by value automatically, even when nested

$z = 1;
$fn = fn($x) => fn($y) => $x * $y + $z;
// Outputs 51
var_export($fn(5)(10));
echo '<br>';
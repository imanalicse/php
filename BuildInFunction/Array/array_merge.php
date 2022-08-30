<?php
/**
 * array_merge — Merge one or more arrays
 * array_merge(array ...$arrays): array
 *
 * Merges the elements of one or more arrays together so that the values of one are appended
 * to the end of the previous one. It returns the resulting array.
 *
 * If the input arrays have the same string keys, then the later value for that key will
 * overwrite the previous one. If, however, the arrays contain numeric keys, the later value will
 * not overwrite the original value, but will be appended.
 *
 * Values in the input arrays with numeric keys will be renumbered with incrementing keys
 * starting from zero in the result array.
 */

 $array1 = array("color" => "red", 2, 4);
$array2 = array("a", "b", "color" => "green", "shape" => "trapezoid", 4);
$result = array_merge($array1, $array2);
echo "<pre>";
print_r($result);
echo "</pre>";
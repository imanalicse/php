<?php
/**
 * array_filter — Filters elements of an array using a callback function
 * array_filter(array $array, ?callable $callback = null, int $mode = 0): array
 */


$formData = [
    "first_name" => "Iman",
    "f-city-name" => "Dhaka",
];
$filterData = array_filter($formData, function($key) {
    return strpos($key, 'f-') === 0;
}, ARRAY_FILTER_USE_KEY);

echo "<pre>";
print_r($filterData);
echo "</pre>";

$arr = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
$filterData = array_filter($arr, function($v, $k) {
    return $k == 'b' || $v == 4;
}, ARRAY_FILTER_USE_BOTH);
echo "<pre>";
print_r($filterData);
echo "</pre>"; // array(2) { ["b"]=> int(2) ["d"]=> int(4) }

$numbers = [6, 7, 8, 9, 10, 11, 12];
$event_numbers = array_filter($numbers, function($number){
    return $number % 2 == 0;
});
echo "<pre>";
print_r($event_numbers);
echo "</pre>";
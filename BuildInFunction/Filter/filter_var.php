<?php
/**
 * filter_var — Filters a variable with a specified filter
 * filter_var(mixed $value, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
 * Return Values: Returns the filtered data, or false if the filter fails.
 */

$email_filter = filter_var('bob@example.com', FILTER_VALIDATE_EMAIL);
echo "<pre>";
print_r(var_dump($email_filter)); // string(15) "bob@example.com"
echo "</pre>";
$url_filter = filter_var('http://example.com', FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
echo "<pre>";
print_r(var_dump($url_filter)); // bool(false)
echo "</pre>";;

// FILTER_SANITIZE_EMAIL
// https://www.php.net/manual/en/filter.filters.sanitize.php
// https://www.php.net/manual/en/filter.filters.validate.php
// https://www.php.net/manual/en/filter.filters.php
echo "<pre>";
print_r(var_dump(filter_var(125.8, FILTER_VALIDATE_INT)));
echo "</pre>";

echo "<pre>";
print_r(filter_var(125.8, FILTER_SANITIZE_NUMBER_INT));
echo "</pre>";
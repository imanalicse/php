<?php
/**
 * filter_var — Filters a variable with a specified filter
 * filter_var(mixed $value, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
 * Return Values: Returns the filtered data, or false if the filter fails.
 */

var_dump(filter_var('bob@example.com', FILTER_VALIDATE_EMAIL)); // string(15) "bob@example.com"
var_dump(filter_var('http://example.com', FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)); // bool(false)

// FILTER_SANITIZE_EMAIL
// https://www.php.net/manual/en/filter.filters.sanitize.php
// https://www.php.net/manual/en/filter.filters.validate.php
// https://www.php.net/manual/en/filter.filters.php
echo "<pre>";
print_r(filter_var('bob@example.com', FILTER_SANITIZE_EMAIL));
echo "</pre>";
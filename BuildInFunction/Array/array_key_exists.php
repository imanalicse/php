<?php
/**
 * array_key_exists — Checks if the given key or index exists in the array
 * array_key_exists(string|int $key, array $array): bool
 * Return value: Returns true on success or false on failure.
 */

$search_array = array('first' => 1, 'second' => 4);
if (array_key_exists('first', $search_array)) {
    echo "The 'first' element is in the array";
}
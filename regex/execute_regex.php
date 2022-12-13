<?php
/**
 * PHP Regex to check date is in YYYY-MM-DD format
 */
$date="2012-09-12";
if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
    return true;
}
else {
    return false;
}
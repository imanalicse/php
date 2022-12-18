<?php

$regex = "/^((0|[1-2])[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/"; // dd/mm/yyyy
$regex = "/^(0[1-9]|1[0-2])\/((0|[1-2])[0-9]|3[0-1])\/[0-9]{4}$/"; // mm/dd/yyyy
$regex = "/^[0-9]{4}\/(0[1-9]|1[0-2])\/((0|[1-2])[0-9]|3[0-1])$/"; // yy/mm/dd

$date="2022/12/31";

if (preg_match($regex, $date)) {
    echo "<pre>";
    print_r("valid");
    echo "</pre>";
}
else {
    echo "<pre>";
    print_r("Invalid");
    echo "</pre>";
}
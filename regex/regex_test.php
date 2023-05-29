<?php

$regex = "/[A-Za-z0-9_-]/";

$value = "premium-Agold_34-fr";

if (preg_match('/^[\w-]+$/', $value)) {
    echo "<pre>";
    print_r("valid");
    echo "</pre>";
}
else {
    echo "<pre>";
    print_r("Invalid");
    echo "</pre>";
}
<?php

use App\Google\GoogleAnalytics\GoogleAnalytics;

include "GoogleAnalytics.php";

$analytics = new GoogleAnalytics();
$analytics->connectToGoogle();


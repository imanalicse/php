<?php
include 'functions.php';
$ch = curl_init("https://www.google.com/?option=test");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
waLog("curl_test");
waLog($output, "curl_test");
echo "<pre>";
print_r($output);
echo "</pre>";



<?php
/**
 * https://www.php.net/manual/en/function.curl-setopt.php
 */
$url = "http://phphub.com/CurlWork/post_receive.php";
$curl = curl_init();
$fields = array(
    'field_name_1' => 'Value 1',
    'field_name_2' => 'Value 2',
    'field_name_3' => 'Value 3'
);
$fields_string = http_build_query($fields);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // true to return the transfer as a string of the return value of curl_exec() instead of outputting it directly.

$data = curl_exec($curl);

$info = curl_getinfo($curl);
echo "<pre>";
print_r($info);
echo "</pre>";

curl_close($curl);

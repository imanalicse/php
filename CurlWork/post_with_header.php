<?php
/**
 * https://www.php.net/manual/en/function.curl-setopt.php
 */
$url = "http://stage-image-api.com/api/v1/auth/token";
$ch = curl_init();
$fields = array(
    'field_name_1' => 'Value 1',
    'field_name_2' => 'Value 2',
    'field_name_3' => 'Value 3'
);
$fields_string = http_build_query($fields);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // true to return the transfer as a string of the return value of curl_exec() instead of outputting it directly.

$authorizationCode = base64_encode('mobileapp:p71EbwVU5QMmNH0YrNpszHGfgn22T4');
$headers = [
   // 'Content-Type: application/json',
    'Authorization: Basic '. $authorizationCode,
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response_data = curl_exec($ch);
$info = curl_getinfo($ch);
$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);
echo "<pre>";
print_r($response_data);
echo "</pre>";
echo "<pre>";
print_r($response_code);
echo "</pre>";
echo "<pre>";
print_r($info);
echo "</pre>";

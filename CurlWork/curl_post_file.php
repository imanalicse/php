<?php
/**
 * https://www.php.net/manual/en/curlfile.construct.php
 */
$request = curl_init('http://phphub.com/php/CurlWork/post_receive.php');

curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request, CURLOPT_SAFE_UPLOAD, true);
curl_setopt($request, CURLOPT_POSTFIELDS, [
    'user' => "Iman Ali",
    'processed_image' => new CURLFile(realpath('../files/S221231AN201-1.jpg'), 'image/jpeg'),
    'thumb_image' => new CURLFile(realpath('../files/sample.jpg'), 'image/jpeg'),
]);
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

echo curl_exec($request);

var_dump(curl_getinfo($request));

curl_close($request);

<?php
$data = [
    "grant_type" => "client_credentials",
    "client_id" => 'abc..',
    "client_secret" => 'abc..',
    "redirect_uri" => 'https://www.google.com/',
];
$data = json_encode($data,  JSON_UNESCAPED_SLASHES);
file_put_contents("token.json", $data);

$file = file_get_contents("token.json");
echo "<pre>";
print_r($file);
echo "</pre>";
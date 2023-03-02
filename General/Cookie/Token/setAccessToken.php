<?php
session_start();
$expires_in = 60 * 2;
$expires_at = time() + $expires_in;
$token_data = [
    'access_token' => uniqid(),
    'expires_in' => $expires_in,
    'expires_at' => $expires_at,
    'scope' => ['add_post',  'edit_post']
];
$token_data = json_encode($token_data);
setcookie('x_access_token', $token_data, $expires_at, "", "", true);
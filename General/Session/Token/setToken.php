<?php
session_start();
$expires_in = 60 * 2;
$token_data = [
    'access_token' => uniqid(),
    'expires_in' => time() + $expires_in,
];

$_SESSION['token_data'] = $token_data;
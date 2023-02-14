<?php
namespace App\Authorization\JWTLocal;

require '../../vendor/autoload.php';

use App\Logger\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$privateKey = file_get_contents("config/jwt.key");

$exp = time() + 60;
$payload = [
    'iss' => 'example.org',
    'aud' => 'example.com',
    'iat' => 1356999524,
    'nbf' => 1357000000,
    'exp' => $exp
];

$jwt = JWT::encode($payload, $privateKey, 'RS256');

echo "<pre>";
print_r($jwt);
echo "</pre>";
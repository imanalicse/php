<?php
namespace App\Authorization\JWTLocal;

require '../../vendor/autoload.php';

use App\Logger\Log;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$publicKey = file_get_contents("config/jwt.pem");

$jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDAsImV4cCI6MTY3NjM3MDAwNn0.D3aIPd6HPGR4grqa1PFPalHIi58uUBCTHbukfKSQnGmWo3KEizTdlGDZ5I7fogyo9zucwAG86NX6Ts4wGyLM2aM81xTs-cujeecy9ZaLdMkTWT_n1trwOpfMRIajEamlh4PPvE1k6kMXeDDRWu52ynNq6yi78YPKLj-WbnM9RZ0';
//$headers = get_headers('http://localhost/php/Authorization/JWTLocal/VerifyToken.php');
//echo "<pre>";
//print_r($headers);
//echo "</pre>";

try {
    $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));
    echo "<pre>";
    print_r($decoded);
    echo "</pre>";
}
catch (\LogicException $e) {
    // errors having to do with environmental setup or malformed JWT Keys
     echo "<pre>";
    print_r('LogicException: ' . $e->getMessage());
    echo "</pre>";
}
catch (\UnexpectedValueException $e) {
    // errors having to do with JWT signature and claims
    echo "<pre>";
    print_r('UnexpectedValueException: ' . $e->getMessage());
    echo "</pre>";
}
die('xxx');
<?php
namespace App\Utils;

class EncryptionDecryption
{
    private static string $security_salt = "900f8c6d3892af94fa956a5b501b68a74d4335af";
    private static string $encrypt_method = "AES-256-CBC";
    private static string $initialization_vector_text = 'Do not change this text';

    public static function encrypt($plaintext) : string
    {
        $encrypt_method = self::$encrypt_method;
        $secret_key = self::$security_salt;
        $key = hash('sha256', $secret_key);
        $initialization_vector = substr(hash('sha256', self::$initialization_vector_text), 0, 16);
        $ciphertext = openssl_encrypt($plaintext, $encrypt_method, $key, OPENSSL_RAW_DATA, $initialization_vector);
        return base64_encode($ciphertext);
    }

    public static function decrypt($ciphertext) : string
    {
        $encrypt_method = self::$encrypt_method;
        $secret_key = self::$security_salt;
        $key = hash('sha256', $secret_key);
        $initialization_vector = substr(hash('sha256', self::$initialization_vector_text), 0, 16);
        $ciphertext = base64_decode($ciphertext);
        return openssl_decrypt($ciphertext, $encrypt_method, $key, OPENSSL_RAW_DATA, $initialization_vector);
    }
}
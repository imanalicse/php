<?php

// This function is to match with java
function encrypt_decrypt_2($action, $string, $default=false)
{
    $output = $default;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'Security.salt'; // from configuration
    $secret_iv = 'Do not change this text';

    // hash
    // it only takes 32 bits in java
    $key = substr(hash('sha256', $secret_key),0, 32);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a
    // warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt')
    {
        $output = openssl_encrypt($string, $encrypt_method, $key, OPENSSL_RAW_DATA, $iv);
        $output = base64_encode($output);
    }
    else
    {
        if ($action == 'decrypt')
        {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, OPENSSL_RAW_DATA, $iv);
        }
    }

    return $output;
}

encrypt_decrypt_2('encrypt', 'imanali.cse@gmail.com');
encrypt_decrypt_2('decrypt', 'emailAddress', null);
<?php

if (isset($_POST['g-recaptcha-response'])) {
    $response = $_POST["g-recaptcha-response"];
    $captcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => '6LdPOlUUAAAAALw0Nr_fyxIgd3l01zhVwZyAtMfr',
        'response' => $_POST["g-recaptcha-response"]
    );
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($data)
        ),
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        )
    );
    $context = stream_context_create($options);
    $verify = @file_get_contents($captcha_url, false, $context);
    $captcha_response = json_decode($verify);
    if (isset($captcha_response->success) && $captcha_response->success) {
        echo "ok";
    } else {
        echo "error";
    }
}
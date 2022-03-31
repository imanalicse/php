<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Re Captcha</title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
</head>

<body>

<?php

$error_msg = "";
$captcha_error_msg = "";
if (isset($_POST['g-recaptcha-response'])) {

    $response = $_POST["g-recaptcha-response"];
    $captcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => '6LdPOlUUAAAAALw0Nr_fyxIgd3l01zhVwZyAtMfr',
        'response' => $_POST["g-recaptcha-response"]
    );
//    $options = array(
//        'http' => array(
//            'method' => 'POST',
//            'content' => http_build_query($data)
//        ),
//        "ssl" => array(
//            "verify_peer" => false,
//            "verify_peer_name" => false,
//        )
//    );
//    $context = stream_context_create($options);
//    $verify = @file_get_contents($captcha_url, false, $context);
//    $captcha_response = json_decode($verify);

    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $captcha_response = curl_exec($verify);
    echo '<pre>';
    print_r($captcha_response);
    echo '</pre>';
    if (isset($captcha_response->success) && $captcha_response->success) {
        echo "validate Captcha";
    } else {
        echo "Invalidated Captcha";
    }
}
?>

<script src='https://www.google.com/recaptcha/api.js'></script>
<div class="container justify-content-center login-container">
    <?php
    if ($captcha_error_msg) {
        echo ' <div class="row justify-content-center alert alert-danger">' . $captcha_error_msg . '</div>';
    }
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="login-form" method="post">
        <div class="form-group row col-md-12 captcha_wrapper">
            <div class="g-recaptcha" data-sitekey="6LdPOlUUAAAAAHhcpOLCUIO1lS7W_Xeic8FhDjoO"></div>
        </div>
        <div class="form-group row justify-content-center">
            <button type="submit" class="btn btn-primary button-padding">Login</button>
        </div>
    </form>
</div>

</body>
</html>
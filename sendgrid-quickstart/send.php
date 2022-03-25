<?php

require '../vendor/autoload.php';

require './../DotEnv.php';
(new DotEnv(__DIR__ . '/.env'))->load();

$email = new \SendGrid\Mail\Mail();
$email->setFrom("iman@bitmascot.com", "Iman Bit Mascot From");
$email->setSubject("Sending with SendGrid is Fun");
$email->addTo("bmimanali@gmail.com", "Bm Iman");
$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
$email->addContent(
    "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
);
$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
try {
    $response = $sendgrid->send($email);
    echo "<pre>";
    print_r($response->statusCode());
    echo "</pre>";
    if ($response->statusCode() == 202) {
        echo "<pre>";
        print_r("Email send");
        echo "</pre>";
    } else {
        $body = json_decode($response->body(), true);
        $errors = $body["errors"];
        foreach ($errors as $error) {
            echo "<pre>";
            print_r($error);
            echo "</pre>";
        }
    }
} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."<br/>";
}
<?php
$email = $_POST['email'];

$result = call('lists/subscribe', array(
    'id' => 'd68e8288ba',
    'email' => array('email' => $email),
    'double_optin' => false,
    'update_existing' => true,
    'replace_interests' => false,
    'send_welcome' => false,
));


function call($method, $args = array())
{
    $api_endpoint = 'https://<dc>.api.mailchimp.com/2.0';
    $api_key = '91691b7aeeb5c750979997ad22b4e04b-us17'; //live

    list(, $datacentre) = explode('-', $api_key);
    $api_endpoint = str_replace('<dc>', $datacentre, $api_endpoint);
    $args['apikey'] = $api_key;
    $url = $api_endpoint . '/' . $method . '.json';

    if (function_exists('curl_init') && function_exists('curl_setopt')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
        $result = curl_exec($ch);

        curl_close($ch);
    } else {
        $json_data = json_encode($args);
        $result = file_get_contents($url, null, stream_context_create(array(
            'http' => array(
                'protocol_version' => 1.1,
                'user_agent' => 'PHP-MCAPI/2.0',
                'method' => 'POST',
                'header' => "Content-type: application/json\r\n" .
                    "Connection: close\r\n" .
                    "Content-length: " . strlen($json_data) . "\r\n",
                'content' => $json_data,
            ),
        )));
    }
//    echo "<pre>";
//    print_r($result);
//    echo "</pre>";
    return $result ? json_decode($result, true) : false;
}
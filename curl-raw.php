<?php

//$url = 'http://www.google.com/search?q=curl';
$url = 'https://rosetta.recordsnsw.com.au/delivery/DeliveryManagerServlet?dps_pid=IE22731';
$options = array(
    CURLOPT_RETURNTRANSFER => true,     // return web page
    CURLOPT_HEADER         => false,    // don't return headers
    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
    CURLOPT_ENCODING       => "",       // handle all encodings
    CURLOPT_USERAGENT      => "spider", // who am i
    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
    CURLOPT_TIMEOUT        => 120,      // timeout on response
    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
);

$ch      = curl_init( $url );
curl_setopt_array( $ch, $options );
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );

echo '<pre>';
print_r($content);
echo '</pre>';

preg_match('/<iframe[^>]+src="([^"]+)"/', $content, $match);
if(!empty($match[1])) {
    echo '<pre>';
    print_r($match[1]);
    echo '</pre>';
}
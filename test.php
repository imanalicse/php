<?php
$url = 'https://rosetta.recordsnsw.com.au/delivery/DeliveryManagerServlet?dps_pid=IE23306&hello=textfdsf';
function getQueryParam($url, $name){
    preg_match("/[\?&]".$name."=([^&#]*)/", $url, $match);
    if(!empty($match) && isset($match[1])){
        return $match[1];
    }
    return '';
}

$pid = getQueryParam($url, 'hello');
echo '<pre>';
print_r($pid);
echo '</pre>';
<?php
$url = 'https://rosetta.recordsnsw.com.au/delivery/DeliveryManagerServlet?dps_pid=IE23306';
function getQueryParam($url, $name){
    preg_match("/[\?&]".$name."=([^&#]*)/", $url, $match);
    if(!empty($match) && isset($match[1])){
        return $match[1];
    }
    return '';
}

$pid = getQueryParam($url, 'dps_pid');
echo '<pre>';
print_r($pid);
echo '</pre>';
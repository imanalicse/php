<?php
$url = "http://www.webalive.com.au/wp-content/uploads/2018/04/finan-aid.jpg";
echo preg_replace("/http:/", "https:", $url);

function addHttps($url, $check = false) {
    $return_url = $url;
    $explode_url = explode(":", $url);
    if(isset($explode_url[0]) && $explode_url[0] != 'https') {
        $return_url = 'https:'.$explode_url[1];
    }
//    if (isset($_SERVER['HTTPS'])  && !empty($_SERVER['HTTPS'])){
//
//    }

    return $return_url;
}
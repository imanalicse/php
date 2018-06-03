<?php
function addHttps($url) {
    $return_url = $url;
    if (isset($_SERVER['HTTPS'])  && !empty($_SERVER['HTTPS'])){
        $return_url = preg_replace("/http:/", "https:", $url);
    }
    return $return_url;
}
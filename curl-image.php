<?php
$url = "https://upload.wikimedia.org/wikipedia/commons/e/eb/Intel-logo.jpg";
//$url = "http://www.bitmascot.com/wp-content/uploads/2016/07/iman-128x130.jpg";

$ch = curl_init(); //open curl handle
curl_setopt($ch, CURLOPT_URL, $url); //set an url
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //do not output directly, use variable
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); //do a binary transfer
curl_setopt($ch, CURLOPT_FAILONERROR, 1); //stop if an error occurred
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$file = curl_exec($ch); //store the content in variable

//echo curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
//echo "<br/>";
//echo curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
//echo "<br/>";
if (!curl_errno($ch)) {

    header("Content-type: " . curl_getinfo($ch, CURLINFO_CONTENT_TYPE) . "");
    header("Content-Length: " . curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD) . "");
    echo $file;
    ?>
    <!--    <img src='data:image/jpeg;,' --><?php //echo $file;
    ?><!--/>-->
    <?php
} else {
    echo 'Curl error: ' . curl_error($ch);
}
curl_close($ch); //close curl handle

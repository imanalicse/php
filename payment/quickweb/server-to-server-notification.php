<?php

# https://quickstream.westpac.com.au/docs/quickweb/#server-to-server-notification

header( "Content-Type: text/plain" );
if ( $_SERVER["REMOTE_ADDR"] != QUICKSTREAM_IP_ADDRESS ) {
    header("HTTP/1.1 403 FORBIDDEN");
    return;
}
if ( !isset($_SERVER['PHP_AUTH_USER'] ) || !isset( $_SERVER['PHP_AUTH_PW'] ) ) {
    header("HTTP/1.1 401 UNAUTHORIZED");
    return;
}
if ( $_SERVER['PHP_AUTH_USER'] != QUICKSTREAM_USERNAME || $_SERVER['PHP_AUTH_PW'] != QUICKSTREAM_PASSWORD ){
    header("HTTP/1.1 403 FORBIDDEN");
    return;
}
if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
    try {
        foreach( $_POST as $key => $value ) {
            // consume
        }
        header("HTTP/1.1 200 OK");
    } catch (Exception $e) {
        header("HTTP/1.1 500 INTERNAL SERVER ERROR");
    }
}
return;

<?php


$post_fields =  [
        'grant_type' => 'client_credentials',
        'client_id' => '2',
        'client_secret' => '0MePOqNHtCie7O7pJGacrlbAsMauQlzkm5PuYxVM',
        'scope' => '',
    ];

    $postFieldsString = http_build_query( $post_fields );
    $curlHandle = curl_init();
    curl_setopt( $curlHandle, CURLOPT_URL, $curl_url);
    curl_setopt( $curlHandle, CURLOPT_POST, count( $post_fields ) );
    curl_setopt( $curlHandle, CURLOPT_POSTFIELDS, $postFieldsString );
    curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, 1 );
    //curl_setopt( $curlHandle, CURLINFO_HEADER_OUT, 1 );
    //Disabled SSL Cert checks
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec( $curlHandle );
    curl_close( $curlHandle );

    echo '<pre>';
    print_r($result);
    echo '</pre>';
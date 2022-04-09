<?php
$date = date('Y-m-d');

header( 'X-UA-Compatible: IE=edge,chrome=1' );
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=test_".$date.".doc");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen( "php://output", "w" );

echo '
    <style>
    p {
        font-size: 14px;
        margin-bottom: 0px;
        line-height: 10px;
    }
    </style>';

$test = '<p>Customer Info</p>';
echo $test;
fclose( $output );
die();
<?php
$date = date('Y-m-d');

header( 'X-UA-Compatible: IE=edge,chrome=1' );
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=product_list_".$date.".csv");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen( "php://output", "w" );
fputcsv( $output, array( 'Customer' ) );

$data[] = array(
    'customer'=>"Iman"
);
foreach( $data as $row ) {
    fputcsv( $output, array( $row['customer'] ));
}


fclose( $output );
die();

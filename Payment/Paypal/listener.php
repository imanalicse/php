<?php
$_POST['abc'] = 'Hello abc';
$data = $_REQUEST;
$data = json_encode($data);
//echo '<pre>';
//print_r($data);
//echo '</pre>';

$file = fopen("listener.txt","w");
echo fwrite($file, $data);
fclose($file);
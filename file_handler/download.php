<?php
include 'FileHandler.php';

$file_directory = dirname( dirname(__FILE__) ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
$file_name = 'sample.jpg';
$file_path = $file_directory . $file_name;
$file_handler = new FileHandler();
//$down_load =  $file_handler->download($file_path);

//$file_handler->createFolder($file_directory.'test');
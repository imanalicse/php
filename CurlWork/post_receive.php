<?php
namespace App\CurlWork;
use App\FileHandler\FileHandler;

require '../vendor/autoload.php';
use App\Logger\Log;

$post_data = $_POST;
Log::write('$post_data', 'curl');
Log::write($_FILES, 'curl');

$file_handler = new FileHandler();
$result = $file_handler->upload($_FILES['processed_image']);
$result = $file_handler->upload($_FILES['thumb_image']);
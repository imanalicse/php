<?php
namespace App\CurlWork;

require '../vendor/autoload.php';
use App\Logger\Log;

$post_data = $_POST;
Log::write('$post_data', 'curl');
Log::write($post_data, 'curl');
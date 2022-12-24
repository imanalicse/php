<?php
namespace App\Payment\AfterPay;

require '../../vendor/autoload.php';
use App\DotEnv;
use App\Logger\Log;

$data = $_REQUEST;
$data = 'Hello Cancel';
echo "<pre>";
print_r($data);
echo "</pre>";
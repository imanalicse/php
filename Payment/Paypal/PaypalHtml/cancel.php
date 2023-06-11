<?php

require '../../../global_config.php';
use App\Logger\Log;

$response_data = $_REQUEST;
Log::write('Paypal cancel data', 'paypal_cancel', 'paypal');
Log::write($response_data, 'paypal_cancel', 'paypal');
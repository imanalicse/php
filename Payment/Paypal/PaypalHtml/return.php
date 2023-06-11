<?php

require '../../../global_config.php';

use App\Logger\Log;

$response_data = $_REQUEST;
Log::write('Paypal return data', 'paypal_return', 'paypal');
Log::write($response_data, 'paypal_return', 'paypal');
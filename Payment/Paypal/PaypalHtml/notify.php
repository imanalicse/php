
<?php
require '../../../global_config.php';

use App\Logger\Log;

$response_data = $_REQUEST;
Log::write('Paypal notify data', 'paypal_notify', 'paypal');
Log::write($response_data, 'paypal_notify', 'paypal');

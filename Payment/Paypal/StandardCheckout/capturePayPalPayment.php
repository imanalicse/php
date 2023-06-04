<?php
namespace App\Payment\Paypal\StandardCheckout;

require '../../../global_config.php';

use App\Payment\Paypal\StandardCheckout\PayPalComponent;
use App\Logger\Log;

try {
    $orderId = $_POST['orderId'];
    $paypal_compo = new PayPalComponent();
    $payment_response = $paypal_compo->executePayPalCapture($orderId);
    if (empty($payment_response)) {
        throw new \Exception('Unable to capture payment');
    }

    Log::write('capturePayPalPayment response::', 'paypal');
    Log::write($payment_response, 'paypal');
    echo json_encode($payment_response);
}
catch (\Exception $exception) {
    Log::write('Error in capturePayPalPayment: '. $exception->getMessage(), 'paypal');
}
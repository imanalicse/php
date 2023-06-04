<?php
namespace App\Payment\Paypal\StandardCheckout;

require '../../../global_config.php';
use App\Payment\Paypal\StandardCheckout\PayPalComponent;
use App\Logger\Log;

try {
    $paypal_compo = new PayPalComponent();
    $order = $paypal_compo->executePaypalOrder();
    if (empty($order)) {
        throw new \Exception('Unable to create paypal order');
    }
    Log::write('createPayPalOrder response:', 'paypal');
    Log::write($order, 'paypal');
    echo json_encode($order);
}
catch (\Exception $exception) {
    Log::write('Error in createPayPalOrder: '. $exception->getMessage(), 'paypal');
}
exit('');
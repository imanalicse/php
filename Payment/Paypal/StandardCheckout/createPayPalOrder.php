<?php
namespace App\Payment\Paypal\StandardCheckout;

require '../../../global_config.php';
use App\Payment\Paypal\StandardCheckout\PayPalComponent;

try {
    $paypal_compo = new \App\Payment\Paypal\StandardCheckout\PayPalComponent();
    $order = $paypal_compo->executePaypalOrder();
    echo "<pre>";
    print_r($order);
    echo "</pre>";
    if (empty($order)) {
        throw new \Exception('Unable to create paypal order');
    }
//    $this->saveLog(PaymentMethod::PAY_PAL, 'pay_pal', 'createPayPalOrder response:');
//    $this->saveLog(PaymentMethod::PAY_PAL, 'pay_pal', $order);
    echo json_encode($order);
}
catch (\Exception $exception) {
    // $this->saveLog(PaymentMethod::PAY_PAL, 'pay_pal_error', 'Error in createPayPalOrder: '. $exception->getMessage());
}
exit('');
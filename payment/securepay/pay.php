<?php
include "../../functions.php";
include "SecurePayHandler.php";

//echo "<pre>";
//print_r($_REQUEST);
//echo "</pre>";
$data = $_REQUEST;
$order_total_amount = 2;
$data['amount'] = intval(floatval($order_total_amount) * 100);
$secure_pay_handler = new SecurePayHandler();
$payment = $secure_pay_handler->securepayMakePaymentByToken($data);
<?php

define("TRANSACTION_MODE", "test");
define("BASE_URL", "http://localhost/codehub/php/payment/paypal");
function getPaypalUrl(){
    if(TRANSACTION_MODE=='live'){
        return 'https://www.paypal.com/cgi-bin/webscr';
    }else{
        return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    }
}

$price = 10;
$orderId = time();
$customer_email = 'iman@bitmascot.com';

$return_url	  = BASE_URL.'/thank-you-page.php?order_id='.$orderId;
$cancel_url	  =	BASE_URL.'/cancel.php';
$notify_url	  =	BASE_URL.'/notify-url.php';
//$paypal_email ='paypal@webmascot.com';
$paypal_email ='iman-facilitator@bitmascot.com';
$querystring  = '';
$querystring .= "?business=".urlencode($paypal_email)."&";
$querystring .= "item_name=WebAlive Website design&";
$querystring .= "item_number=1&";
$querystring .= "invoice='".$orderId."'&";
$querystring .= "amount=".urlencode($price)."&";
$querystring .= "cmd=_xclick&";
$querystring .= "no_note=1&";
$querystring .= "charset=utf-8&";
$querystring .= "currency_code=AUD&";
$querystring .= "paymentaction=sale&";
$querystring .= "bn=Varien_Cart_WPS_AU&";
$querystring .= "rm=2&";
$querystring .= "lc=AU&";
$querystring .= "first_name=''&";
$querystring .= "last_name=''&";
$querystring .= "zip=''";
$querystring .= "state=''&";
$querystring .= "zip=''&";
$querystring .= "address1=''&";
$querystring .= "payer_email=".$customer_email."&";


$querystring .= "return=".urlencode(stripslashes($return_url))."&";
$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
$querystring .= "notify_url=".urlencode($notify_url);
//$paypalURL =;
$paypalURL = getPaypalUrl(); //Test PayPal API URL
$paypalID = $paypal_email; //Business Email

header('Location:'.$paypalURL.$querystring);
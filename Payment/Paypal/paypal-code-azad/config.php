<?php
// sandbox test environment
$paypal_endpoint="https://www.sandbox.paypal.com/cgi-bin/webscr";
$paypal_merchant="azad@bitmascot.com";

// live payment environment
/*$paypal_endpoint="https://www.paypal.com/cgi-bin/webscr";
$paypal_merchant="real_merchant@real_domain.com";*/


// webapp url configuration
$domain_uri = 'http://larry.webmascot.com/test/paypal_test';
$webapp_return = $domain_uri."/return.php";
$webapp_cancel = $domain_uri."/cancel.php";
$webapp_notify = $domain_uri."/notify.php";

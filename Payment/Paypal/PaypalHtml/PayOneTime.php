<?php
// https://developer.paypal.com/api/nvp-soap/paypal-payments-standard/integration-guide/formbasics/
// https://www.sandbox.paypal.com/cgi-bin/webscr
// https://www.paypal.com/cgi-bin/webscr

$domain_uri = 'http://phphub.com/php/Payment/Paypal/PaypalHtml';
$webapp_return = $domain_uri."/return.php";
$webapp_cancel = $domain_uri."/cancel.php";
$webapp_notify = $domain_uri."/notify.php";
?>

<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="business" value="sb-bmimanali@business.com.au" />

    <input type="hidden" name="return" value="<?php echo $webapp_return; ?>" />
    <input type="hidden" name="cancel_return" value="<?php echo $webapp_cancel; ?>" />
    <input type="hidden" name="notify_url" value="<?php echo $webapp_notify; ?>" />
    <input type="hidden" name="cmd" value="_xclick" />
    <input type="hidden" name="no_note" value="1" />
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" value="USD" name="currency_code">
    <input type="hidden" name="country" value="USA" />

    <input type="hidden" name="item_name" value="Sample Item" />
    <label>Item Amount:</label>
    <input type="text" name="amount" value="1.00" />

    <input type="hidden" name="custom" value="" />
    <input type="hidden" name="email" value="sb-bmimanali@personal.com.au" />
    <input type="submit" name="submit" value="Pay" />
</form>

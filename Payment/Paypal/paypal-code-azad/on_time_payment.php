<?php require_once 'config.php'; ?>

<form action="<?php echo $paypal_endpoint; ?>" name="contributiontracking" method="post">
    <input type="hidden" name="business" value="<?php echo $paypal_merchant; ?>" />

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
    <input type="submit" name="submit" value="Pay" />
</form>
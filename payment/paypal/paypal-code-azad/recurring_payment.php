<?php require_once "config.php"; ?>

<form action="<?php echo $paypal_endpoint; ?>" name="contributiontracking" method="post">
    <input type="hidden" value="<?php echo $paypal_merchant; ?>" name="business">

    <input type="hidden" name="return" value="<?php echo $webapp_return; ?>" />
    <input type="hidden" name="cancel_return" value="<?php echo $webapp_cancel; ?>" />
    <input type="hidden" name="notify_url" value="<?php echo $webapp_notify; ?>" />
    <input type="hidden" name="cmd" value="_xclick-subscriptions" />
    <input type="hidden" name="no_note" value="1" />
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="country" value="USA" />

    <input type="hidden" value="Sample Item" name="item_name">
    <!-- for trial/first term -->
    <!--
    <input type="hidden" name="a1" value="0.00" />
    <input type="hidden" name="p1" value="2" />
    <input type="hidden" name="t1" value="D" />
    -->
    <!-- for next phase term -->
    <!--
    <input type="hidden" name="a2" value="2.00" />
    <input type="hidden" name="p2" value="3" />
    <input type="hidden" name="t2" value="D" />
    -->
    <!-- for final phase term -->
    <label>Recurring Amount:</label>
    <input type="text" name="a3" value="3.00" />
    <input type="hidden" name="p3" value="1" />
    <label>Cycle Span:</label>
    <select name="t3">
        <option value="D">Daily</option>
        <option value="W" selected>Weekly</option>
        <option value="M">Monthly</option>
        <option value="Y">Yearly</option>
    </select>

    <input type="hidden" name="src" value="1" />
    <label>Total Cycle:</label>
    <input type="text" name="srt" value="2" />
    <input type="hidden" name="sra" value="1" />

    <input type="hidden" name="custom" value="" />
    <input type="submit" name="submit" value="Pay" />
</form>
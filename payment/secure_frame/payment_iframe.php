<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/sha1-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script>
        $(function() {
            var merchantId = $("#merchantID").val();
            var password =$("#merchantPassword").val();
            var txnType = "0";
            var primaryRef = $("#primary_ref").val();//"Test Reference";
            var amount = $("#amount").val();

            var timestamp = moment.utc().format("YYYYMMDDHHMMSS");
            var fingerprint = CryptoJS.enc.Hex.stringify(
                CryptoJS.SHA1([merchantId, password, txnType, primaryRef, amount, timestamp].join("|"))
            );
            $('input[name="merchant_id"]').val(merchantId);
            $('input[name="txn_type"]').val(txnType);
            $('input[name="fp_timestamp"]').val(timestamp);
            $('input[name="fingerprint"]').val(fingerprint);
            $('input[name="return_url_text"]').val('Confirm');
            $("form").submit();
        });
    </script>
</head>
<body>
<?php
    include "include.php";
    $base_url = get_base_url();
?>
<form action="https://test.payment.securepay.com.au/secureframe/invoice" method="post">
    <input type="hidden" name="merchantID" id="merchantID" value="ABC0001">
    <input type="hidden" name="merchantPassword" id="merchantPassword" value="abc123">

    <input type="hidden" name="bill_name" value="transact">
    <input type="hidden" name="merchant_id">
    <input type="hidden" name="txn_type">
    <input id="primary_ref"  type="hidden" name="primary_ref" value="123">
    <input id="amount" type="hidden" name="amount"  value="200">
    <input type="hidden" name="fp_timestamp">
    <input type="hidden" name="fingerprint">
    <input type="hidden" name="currency" value="AUD">
    <input type="hidden" name="return_url" value="<?php echo $base_url; ?>/secure_frame/return_url.php">
    <input type="hidden" name="callback_url" value="<?php echo $base_url; ?>/secure_frame/callback_url.php">
    <input type="hidden" name="cancel_url" value="<?php echo $base_url; ?>/secure_frame/my_account.php">
<!--    <input type="hidden" name="display_cardholder_name" value="yes">-->

    <input type="hidden" name="return_url_text" value="Confirm">
    <input type="hidden" name="return_url_target" value="parent">
    <input type="hidden" name="title" value="Payment Page">
    <input type="hidden" name="primary_ref_name" value="Order Number">
    <input type="hidden" name="template" value="responsive">
    <input type="hidden" name="display_receipt" value="no">
    <input type="hidden" name="confirmation" value="no">
    <input type="hidden" name="page_style_url" value="<?php echo $base_url; ?>/secure_frame/css/secure_frame.css">
</form>
</body>
</html>
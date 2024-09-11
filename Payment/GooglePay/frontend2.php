<?php
require '../../global_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Google PAY</title>
    <script src="https://pay.google.com/gp/p/js/pay.js" onload="console.log('TODO: add onload function')">
    </script>
    <script src="google_pay2.js"></script>
</head>
<body>
<input type="hidden" id="stripe_public_key" value="<?php echo getenv('STRIPE_PUBLIC_KEY'); ?>">
<!--<div id="container"></div>-->
<div id="google-checkout-pay" button-type="pay" button-color="black"></div>
</body>
</html>

<style>
    #google-checkout-pay.not-ready {
        width: auto;
        height: auto;
        overflow: visible;
    }
</style>
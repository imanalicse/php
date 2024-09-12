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
    <script src="google_pay_final.js"></script>
</head>
<body>
<input type="hidden" id="stripe_public_key" value="<?php echo getenv('STRIPE_PUBLIC_KEY'); ?>">
<div id="container"></div>
</body>
</html>
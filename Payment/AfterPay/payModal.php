<html>
<head>
  <script type="text/javascript" src="https://portal.sandbox.afterpay.com/afterpay.js" defer></script>
  <script src="after_pay.js?v=<?php echo time(); ?>" defer></script>
</head>
<body>
    <?php require 'common_function.php'; ?>
  <div class="payment_message js_payment_message error-message-wrap"></div>
  <button id="afterpay-button">
      $<?php echo paymentAmount(); ?> Pay
  </button>
</body>
</html>

<html>
<head>
  <script type="text/javascript" src="https://portal.sandbox.afterpay.com/afterpay.js"></script>
</head>
<body>
  <button id="afterpay-button">
    Afterpay it!
  </button>
  <script type="text/javascript">
    document.getElementById("afterpay-button").addEventListener("click", function() {
      AfterPay.initialize({countryCode: "AU"});
      // To avoid triggering browser anti-popup rules, the AfterPay.open()
      // function must be directly called inside the click event listener
      AfterPay.open();
      // If you don't already have a checkout token at this point, you can
      // AJAX to your backend to retrieve one here. The spinning animation
      // will continue until `AfterPay.transfer` is called.
      // If you fail to get a token you can call AfterPay.close()
      AfterPay.onComplete = function(event) {
        console.log('event', event)
        if (event.data.status == "SUCCESS") {
          // The consumer confirmed the payment schedule.
          // The token is now ready to be captured from your server backend.
          console.log("Success")
        } else {
          // The consumer cancelled the payment or closed the popup window.
          console.log("cancelled or closed")
        }
      }
      setTimeout(function () {
          AfterPay.transfer({token: "001.j3ca929tc579v9a170sb4ask0qiqpcs21a2fdp4c9utvi1ms"});
      }, 3000)
    });
  </script>
</body>
</html>s

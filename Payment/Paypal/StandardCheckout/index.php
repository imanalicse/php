<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=AUJFeA_eHWay7oKWMbBWXARm7t4kmuA3qj1kdBE-kAyBQAHxBg6D9EkjtsHgqBZHQbowa1gIKXZYV8bi&currency=USD"></script>
    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
    <script>
      paypal
        .Buttons({
          // Sets up the transaction when a payment button is clicked
          createOrder: function () {
            return fetch("/api/orders", {
              method: "post",
              // use the "body" param to optionally pass additional order information
              // like product skus and quantities
              body: JSON.stringify({
                cart: [
                  {
                    sku: "<YOUR_PRODUCT_STOCK_KEEPING_UNIT>",
                    quantity: "<YOUR_PRODUCT_QUANTITY>",
                  },
                ],
              }),
            })
              .then((response) => response.json())
              .then((order) => {
                console.log('Order', order)
                return order.id;
              });
          },
          // Finalize the transaction after payer approval
          onApprove: function (data) {
            return fetch(`/api/orders/${data.orderID}/capture`, {
              method: "post",
            })
              .then((response) => response.json())
              .then((orderData) => {
                // Successful capture! For dev/demo purposes:
                console.log(
                  "Capture result",
                  orderData,
                  JSON.stringify(orderData, null, 2)
                );
                var transaction =
                  orderData.purchase_units[0].payments.captures[0];
                alert(
                  "Transaction " +
                    transaction.status +
                    ": " +
                    transaction.id +
                    "\n\nSee console for all available details"
                );
                // When ready to go live, remove the alert and show a success message within this page. For example:
                // var element = document.getElementById('paypal-button-container');
                // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                // Or go to another URL:  actions.redirect('thank_you.html');
              });
          },

        })
        .render("#paypal-button-container");
    </script>
  </body>
</html>

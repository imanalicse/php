paypal
    .Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: function () {
            console.log("createOrder called")
            return fetch("createPayPalOrder.php", {
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
                    console.log('createPayPalOrderResponse', order)
                    return order.id;
                });
        },
        // Finalize the transaction after payer approval
        onApprove: function (data) {
            console.log("onApproveData", data);
            var post_data = new FormData();
            post_data.append('orderId', data.orderID)
            //  return fetch(`/api/orders/${data.orderID}/capture`, {
            return fetch("capturePayPalPayment.php", {
                method: "post",
                // headers: {
                //     'Content-Type': 'application/json',
                // },
                body: post_data
            })
                .then((response) => response.json())
                .then((orderData) => {
                    // Successful capture! For dev/demo purposes:
                    console.log(
                        "Capture result",
                        orderData,
                        JSON.stringify(orderData, null, 2)
                    );
                    var transaction = orderData.purchase_units[0].payments.captures[0];
                    console.log('transaction', transaction)
                    // When ready to go live, remove the alert and show a success message within this page. For example:
                    var element = document.getElementById('paypal-button-container');
                    element.innerHTML = '<h3>Thank you for your payment! ' + transaction.id + ' </h3>';
                    // Or go to another URL:  actions.redirect('thank_you.html');
                });
        },
    })
    .render("#paypal-button-container");
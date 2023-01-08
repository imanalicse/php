document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("afterpay-button").addEventListener("click", function() {
        afterPayPayment();
    });
});

function afterPayPayment() {
    AfterPay.initialize({countryCode: "AU"});
    // To avoid triggering browser anti-popup rules, the AfterPay.open()
    // function must be directly called inside the click event listener
    AfterPay.open();
    // If you don't already have a checkout token at this point, you can
    // AJAX to your backend to retrieve one here. The spinning animation
    // will continue until `AfterPay.transfer` is called.
    // If you fail to get a token you can call AfterPay.close()

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // document.getElementById("demo").innerHTML = this.responseText;
            console.log('onreadystatechange', this);
            console.log(this.responseText)
        }
    };
    xhr.open("GET", "getToken.php", true);
    xhr.send();

    /*
       $.ajax({
           type: 'GET',
           url: 'getToken.php',
           beforeSend: function (request) {
               $(".js_payment_message").empty();
           },
           complete: function (request, json) {
           },
           success: function (resp) {
               try {
                   console.error('resp', resp)
                   // let response = JSON.parse(resp);
                   // if (response.token) {
                   //     let token = response.token;
                   //     AfterPay.transfer({token: token});
                   // }
                   // else {
                   //     AfterPay.close();
                   //     if (response.message) {
                   //         $(".js_payment_message").html("<span class='error'>" + response.message + "</span>");
                   //     }
                   //     if (response.redirect) {
                   //         setTimeout(function () {
                   //              window.location.replace(response.redirect);
                   //          }, 4000);
                   //     }
                   // }
               }
               catch (e) {
                   console.error(e);
                   AfterPay.close();
               }
           }
       });


       AfterPay.onComplete = function(event) {
           console.log('onComplete event', event)
           if (event.data.status == "SUCCESS") {
               // The consumer confirmed the payment schedule.
               // The token is now ready to be captured from your server backend.
               let orderToken = event.data.orderToken;
               $.ajax({
                   type: 'POST',
                   url: BASE_URL + 'afterpay/capturePayment',
                   data: {
                       token: orderToken,
                       responseData: event
                   },
                   headers : {
                       'X-CSRF-Token': csrfToken
                   },
                   beforeSend: function (request) {
                       showLoader();
                   },
                   complete: function (request, json) {
                       hideLoader();
                   },
                   success: function (resp) {
                       try {
                           // console.log('capturePaymentResp', resp)
                           let response = JSON.parse(resp);
                           // $(".payment_message").show();
                           // $(".payment_message").html(response.message);
                           console.log('capturePaymentResp', response)
                           if (response.status) {
                               $(".js_payment_message").html("<span class='success'>" + response.message + "</span>");
                               if (response.redirect) {
                                   window.location.replace(response.redirect);
                               }
                           }
                           else {
                               $(".js_payment_message").html("<span class='error'>" + response.message + "</span>");
                               if (response.redirect) {
                                   setTimeout(function () {
                                       window.location.replace(response.redirect);
                                   }, 4000);
                               }
                           }
                       }
                       catch (e) {
                           console.error(e);
                       }
                   }
               });
           }
           else {
               // The consumer cancelled the payment or closed the popup window.
           }
       }
       */
}
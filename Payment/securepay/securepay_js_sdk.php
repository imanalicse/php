<?php
    //use App\DotEnv;
    require 'DotEnv.php';
    (new DotEnv(__DIR__ . '/.env'))->load();
?>
<!doctype html>
<html>
    <head>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
    </head>
  <body>
    <form onsubmit="return false;">
      <div id="securepay-ui-container"></div>
      <button onclick="mySecurePayUI.tokenise();">Submit</button>
      <button onclick="mySecurePayUI.reset();">Reset</button>
    </form>
    <script id="securepay-ui-js" src="https://payments-stest.npe.auspost.zone/v3/ui/client/securepay-ui.min.js"></script>
    <script type="text/javascript">
//document.addEventListener("DOMContentLoaded", function(event) {
    var mySecurePayUI = new securePayUI.init({
        containerId: 'securepay-ui-container',
        scriptId: 'securepay-ui-js',
        clientId: '<?php echo getenv("SECURE_PAY_CLIENT_ID"); ?>',
        merchantCode: '<?php echo getenv("SECURE_PAY_MERCHANT_CODE") ?>',
        card: {
            allowedCardTypes: ['visa', 'mastercard'],
            showCardIcons: true,
            onCardTypeChange: function (cardType) {
                // card type has changed
            },
            onBINChange: function (cardBIN) {
                // card BIN has changed
            },
            onFormValidityChange: function (valid) {
                // form validity has changed
            },
            onTokeniseSuccess: function (tokenisedCard) {
                // card was successfully tokenised or saved card was successfully retrieved
                console.log("tokenisedCard", tokenisedCard);
                makePaymentTithToken(tokenisedCard);
            },
            onTokeniseError: function (errors) {
                // tokenization failed
            }
        },
        style: {
            backgroundColor: 'transparent',
            label: {
                font: {
                    // family: 'Arial, Helvetica, sans-serif',
                    // size: '1.1rem',
                    // color: 'darkblue'
                }
            },
            input: {
                font: {
                    // family: 'Arial, Helvetica, sans-serif',
                    // size: '1.1rem',
                    // color: 'darkblue'
                }
            }
        },
        onLoadComplete: function () {
            // the UI Component has successfully loaded and is ready to be interacted with
        }
    });

    function makePaymentTithToken(data) {
        $.ajax({
            url: "http://localhost:8000/pay.php",
            method: 'POST',
            data: data,
            type: 'json',
            success: function (resp) {
                console.log('resp', resp);
            }
        });
    }
//});

    </script>
  </body>
</html>
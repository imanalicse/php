QuickstreamAPI.init({
    publishableApiKey: ""
});
let is_initialize = QuickstreamAPI.isInitialised();

let supplier_code = "";
let west_pac_payment_form = document.querySelector("#west_pac_payment_form");
console.log('west_pac_payment_form', west_pac_payment_form)


// This for trusted frame =============
var trustedFrame;

var options = {
    config: {
        supplierBusinessCode: supplier_code // This is a required config option
    }
};

QuickstreamAPI.creditCards.createTrustedFrame( options, function( errors, data ) {
    if ( errors ) {
        // Handle errors here
    }
    else {
        trustedFrame = data.trustedFrame
    }
});
document.addEventListener("DOMContentLoaded", function () {
    $("#west_pac_pay_btn").on("click", function () {
        console.log('clickedddddddd')
        trustedFrame.submitForm(function (errors, data) {
            console.log('submit_errors', errors)
            console.log('data', data)
            if (errors) {
                // Handle errors here
            }
            else {
                console.log("singleUseTokenId is " + data.singleUseToken.singleUseTokenId); // singleUseTokenId is TOKEN_VALUE

                $.ajax({
                    type: 'POST',
                    url: 'transaction.php',
                    data: {
                        token: data.singleUseToken.singleUseTokenId,
                        data: data
                    },
                    beforeSend: function (request) {

                    },
                    complete: function (request, json) {

                    },
                    success: function (resp) {
                        try {
                            console.log('resp', resp)
                        }
                        catch (e) {
                            console.error(e);
                        }
                    }
                });

            }
        });
    })
});

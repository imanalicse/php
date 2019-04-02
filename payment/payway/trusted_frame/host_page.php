<!DOCTYPE html>
<html>
<body>
<!-- this form will POST a single use token to your server -->
<form action="/process-payment" method="post">
    <div id="payway-credit-card"></div>
    <input id="payway-cc-submit" type="submit" disabled="true"/>
</form>
<script src="https://api.payway.com.au/rest/v1/payway.js">
</script>
<script type="text/javascript">
    var submit = document.getElementById('payway-cc-submit');

    var createdCallback = function( err, frame ) {
        if ( !err ) {
            console.log('Yes');
        }
    };

    var style = {
        'div.payway-card' : { 'background-color': '#404040',
            'border-radius': '0em' },
        '.payway-card label' : { 'color' : 'white' },
        '.payway-card legend': { 'color' : 'white' },
        '.payway-card input' : { 'color' : '#202020' },
        '.payway-card select': { 'color' : '#202020' }
    };

    payway.createCreditCardFrame({
            publishableApiKey: 'T11915_PUB_gjjg685bbrqkvwzb2jzw8ye4wu8xvfigkzfqmqrshzy28y67fp765pqjangi',
            onValid: function() { submit.disabled = false; },
            onInvalid: function() { submit.disabled = true; },
            style: style,
            layout: 'narrow'
        },
        createdCallback
    );
//


</script>
</body>
</html>
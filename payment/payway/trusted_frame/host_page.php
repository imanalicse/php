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
    payway.createCreditCardFrame({
        publishableApiKey: 'T11915_PUB_gjjg685bbrqkvwzb2jzw8ye4wu8xvfigkzfqmqrshzy28y67fp765pqjangi',
        onValid: function() { submit.disabled = false; },
        onInvalid: function() { submit.disabled = true; }
    });
</script>
</body>
</html>
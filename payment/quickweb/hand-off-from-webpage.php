<?php
//define('HAND_OFF_URL', 'https://quickweb.westpac.com.au/OnlinePaymentServlet3'); //LIVE
define('HAND_OFF_URL', 'https://quickweb.support.qvalent.com/OnlinePaymentServlet3'); //Test
define('communityCode', '');
?>
<form action="<?php echo HAND_OFF_URL ?>" method="POST">
    <input type="hidden" name="token" value="OicksakIMkD3OiZpyE7MadwJkZSrSqgjviXCEomVD3ZzEmZ6Vlxecg"/>
    <input type="hidden" name="communityCode" value="<YOUR_COMMUNITY_CODE>"/>
    <input type="submit" value="Make Payment"/>
</form>
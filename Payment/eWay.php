<div class='main_body_text'><div id="checkout_process_page">
    <form  id="checkout_confirmation" method="post" action="https://www.eway.com.au/gateway/payment.asp" name="checkout_confirmation">
		<input type="hidden" name="ewayCustomerID" value="87654321" />
		<input type="hidden" name="ewayTotalAmount" value="16290" />
		<input type="hidden" name="ewayCustomerFirstName" value="Selim" />
		<input type="hidden" name="ewayCustomerLastName" value="Reza" />
		<input type="hidden" name="ewayCustomerEmail" value="selim@webalive.com.au" />
		<input type="hidden" name="ewayCustomerAddress" value="9 South Yarra" />
		<input type="hidden" name="ewayCustomerPostcode" value="3141" />
		<input type="hidden" name="ewayCustomerInvoiceDescription" value="AKG D5 dynamic microphone(qty: 1)" />
		<input type="hidden" name="ewayCustomerInvoiceRef" value="72" />
		<input type="hidden" name="eWAYURL" value="http://balkey.webmascot.com/mph/index.php?main_page=checkout_success&amp;returnFromEway=ewaypayment&amp;orders_id=72" />
		<input type="hidden" name="ewayOption1" value="72" />
		<input type="hidden" name="eWAYSiteTitle" value="Music Powerhouse" />
		<input type="hidden" name="eWAYAutoRedirect" value="1" />
		<p class='submit_eway_btn_prefix'>Please click below to process your transaction</p>
		<input type="submit" id="submit_eway_btn" name="submit_eway" value="Process Secure Credit Card Transaction using eWAY">
		<p class='submit_eway_btn_suffix'>Please use eWAY Secure Process to pay your order.<br /> Your order won't be confirm until your payment hasn't be validate by eWAY. <br />Thank you! </p>
	</form>
		<script type="text/javascript">document.checkout_confirmation.submit();</script>		
</div>
<!--Response-->
if ($_POST["ewayTrxnStatus"] == 'False') {
            $messageStack->add_session('checkout_payment', 'Thank you for chosing eWAY Secure Process! We are sorry but the transaction failed.<br />Please try again.', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
<!--End of Response-->


developer account
engr.imanali@gmail.com
Test_123456

marchant account
imanali.cse@gmail.com
Test_123456

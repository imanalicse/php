<?php
$xml_request =  '<ewaygateway>
      <ewayCustomerID> 87654321 </ewayCustomerID> 
      <ewayTotalAmount> 10 </ewayTotalAmount> 
      <ewayCustomerFirstName> Jackie </ewayCustomerFirstName> 
      <ewayCustomerLastName> Chan </ewayCustomerLastName> 
      <ewayCustomerEmail> chan@webactive.com.au </ewayCustomerEmail> 
      <ewayCustomerAddress> 123 Sesame St </ewayCustomerAddress> 
      <ewayCustomerPostcode> 2345 </ewayCustomerPostcode> 
      <ewayCustomerInvoiceDescription> Red socks </ewayCustomerInvoiceDescription> 
      <ewayCustomerInvoiceRef> 0123 - abc </ewayCustomerInvoiceRef> 
      <ewayCardHoldersName> Jackie Chan </ewayCardHoldersName> 
      <ewayCardNumber> 4444333322221111 </ewayCardNumber> 
      <ewayCardExpiryMonth> 04 </ewayCardExpiryMonth> 
      <ewayCardExpiryYear> 12 </ewayCardExpiryYear> 
      <ewayTrxnNumber> 987654321 </ewayTrxnNumber> 
      <ewayOption1></ewayOption1>
      <ewayOption2></ewayOption2>
      <ewayOption3></ewayOption3>  
</ewaygateway>';


$ch = curl_init("https://www.eway.com.au/gateway/xmltest/testpage.asp");
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $xml_request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
$xml_response=curl_exec($ch);
curl_close($ch);

echo "<pre>";
print_r($xml_response);
echo "</pre>";
?>

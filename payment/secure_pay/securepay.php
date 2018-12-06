<?php
ini_set('max_execution_time', 0);

$date = new DateTime();

$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<SecurePayMessage>";
    $xml.=  "<MessageInfo>";
        $xml.="<messageID>8af793f9af34bea0cf40f5fb750f64</messageID>";
        $xml.="<messageTimestamp>" . $date->getTimestamp() . "</messageTimestamp>";
        $xml.="<timeoutValue>60</timeoutValue>";
        $xml.="<apiVersion>xml-4.2</apiVersion>";
    $xml.="</MessageInfo>";

    $xml.="<MerchantInfo>";
        $xml.="<merchantID>ABC0001</merchantID>";
        $xml.="<password>abc123</password>";
    $xml.="</MerchantInfo>";

    $xml.="<RequestType>Payment</RequestType>";
    $xml.="<Payment>";
        $xml.="<TxnList count='1'>";
            $xml.="<Txn ID='1'>";
                $xml.="<txnType>0</txnType>";
                $xml.="<txnSource>23</txnSource>";
                $xml.="<amount>2600</amount>";
                $xml.="<recurring>No</recurring>";
                $xml.="<currency>AUD</currency>";
                $xml.="<purchaseOrderNo>125</purchaseOrderNo>";

                $xml.="<CreditCardInfo>";
                    $xml.= "<cardType>3</cardType>";
                    $xml.= "<cardNumber>4444333322221111</cardNumber>";
                    $xml.= "<expiryDate> 08/23</expiryDate>";
                    $xml.= "<cvv>123</cvv>";
                $xml.="</CreditCardInfo>";

            $xml.="</Txn>";
        $xml.="</TxnList>";
    $xml.="</Payment>";
$xml.="</SecurePayMessage>";



$url = "https://test.api.securepay.com.au/xmlapi/payment"; //test mode
//$url = "https://payment.securepay.com.au/secureframe/payment";  //live mode

$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $url);
curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch, CURLOPT_POST, true );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
$result = curl_exec($ch);
curl_close($ch);


$xml = simplexml_load_string($result);
$json = json_encode($xml);
$xml_data= json_decode($json,TRUE);

$data['transactionDetails'] = array(
    'order_id' => 10,
    'merchantID' => $xml_data['MerchantInfo']['merchantID'],
    'txnID' => $xml_data['Payment']['TxnList']['Txn']['txnID'],
    'txnType' => $xml_data['Payment']['TxnList']['Txn']['txnType'],
    'txnSource' => $xml_data['Payment']['TxnList']['Txn']['txnSource'],
    'pan' => $xml_data['Payment']['TxnList']['Txn']['CreditCardInfo']['pan'],
    'expiryDate' => $xml_data['Payment']['TxnList']['Txn']['CreditCardInfo']['expiryDate'],
    'cardType' => $xml_data['Payment']['TxnList']['Txn']['CreditCardInfo']['cardType'],
    'cardDescription' => $xml_data['Payment']['TxnList']['Txn']['CreditCardInfo']['cardDescription'],
    'amount' => $xml_data['Payment']['TxnList']['Txn']['amount'],
    'currency' => $xml_data['Payment']['TxnList']['Txn']['currency'],
    'approved' => $xml_data['Payment']['TxnList']['Txn']['approved'],
    'responseCode' => $xml_data['Payment']['TxnList']['Txn']['responseCode'],
    'responseText' => $xml_data['Payment']['TxnList']['Txn']['responseText'],
    'settlementDate' => $xml_data['Payment']['TxnList']['Txn']['settlementDate'],
    'statusCode' => $xml_data['Status']['statusCode'],
    'statusDescription' => $xml_data['Status']['statusDescription'],
    'thinlinkResponseCode' => $xml_data['Payment']['TxnList']['Txn']['thinlinkResponseCode'],
    'thinlinkResponseText' => $xml_data['Payment']['TxnList']['Txn']['thinlinkResponseText'],
    'thinlinkEventStatusCode' => $xml_data['Payment']['TxnList']['Txn']['thinlinkEventStatusCode'],
    'thinlinkEventStatusText' => $xml_data['Payment']['TxnList']['Txn']['thinlinkEventStatusText']
);

echo '<pre>';
print_r($xml_data);
echo '</pre>';

echo '<pre>';
print_r($data);
echo '</pre>';







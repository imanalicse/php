<?php
define("TRANSACTION_MODE", "test");

$custom = 
$payment = sendToPayment();

function sendToPayment($customer, $price)
{
    $transaction_mode = TRANSACTION_MODE;
    if ($transaction_mode == 'live') {
        $curlUrl = "https://www.eway.com.au/gateway_cvn/xmlpayment.asp";
        //$eWayCustomerId = "15328035"; // live
    } else if ($transaction_mode == 'test') {
        $curlUrl = "https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp";
        $eWayCustomerId = "87654321"; /* test account */
    }
    $eWaySOAPActionURL = "https://www.eway.com.au/gateway/managedpayment";

    $eWayTotalAmount = $price * 100; // $price; /* 1$ = 100 cent */
    $ewayCustomerEmail = trim($customer['email']);

    $ewayCardNumber = trim($customer['card_number']);
    $ewayCustomerFirstName = trim($customer['first_name']);
    $ewayCardHoldersName = trim($customer['first_name']);
    $ewayCardExpiryMonth = trim($customer['card_expiry_month']);
    $ewayCardExpiryYear = trim($customer['card_expiry_year']);
    $ewayCVN = trim($customer['cvv']);

    $directXML = "<ewaygateway>" .
        "<ewayCustomerID>" . $eWayCustomerId . "</ewayCustomerID>" .
        "<ewayTotalAmount>" . $eWayTotalAmount . "</ewayTotalAmount>" .
        "<ewayCustomerFirstName>" . $ewayCustomerFirstName . "</ewayCustomerFirstName>" .
        "<ewayCustomerLastName></ewayCustomerLastName>" .
        "<ewayCustomerEmail>" . $ewayCustomerEmail . "</ewayCustomerEmail>" .
        "<ewayCustomerAddress></ewayCustomerAddress>" .
        "<ewayCustomerPostcode></ewayCustomerPostcode>" .
        "<ewayCustomerInvoiceDescription></ewayCustomerInvoiceDescription>" .
        "<ewayCustomerInvoiceRef> Invoice Reference </ewayCustomerInvoiceRef>" .
        "<ewayCardHoldersName>" . $ewayCardHoldersName . "</ewayCardHoldersName>" .
        "<ewayCardNumber>" . $ewayCardNumber . "</ewayCardNumber>" .
        "<ewayCardExpiryMonth>" . $ewayCardExpiryMonth . "</ewayCardExpiryMonth>" .
        "<ewayCardExpiryYear>" . $ewayCardExpiryYear . "</ewayCardExpiryYear>" .
        "<ewayCVN>" . $ewayCVN . "</ewayCVN>" .
        "<ewayTrxnNumber></ewayTrxnNumber>" .
        "<ewayOption1></ewayOption1>" .
        "<ewayOption2></ewayOption2>" .
        "<ewayOption3></ewayOption3>" .
        "</ewaygateway>";

    $result = __makeCurlCall(
        $curlUrl, /* CURL URL */
        "POST", /* CURL CALL METHOD */
        array( /* CURL HEADERS */
            "Content-Type: text/xml; charset=utf-8",
            "Accept: text/xml",
            "Pragma: no-cache",
            "SOAPAction: " . $eWaySOAPActionURL,
            "Content_length: " . strlen(trim($directXML))
        ),
        null, /* CURL GET PARAMETERS */
        $directXML /* CURL POST PARAMETERS AS XML */
    );


    if ($result != null && isset($result["response"])) {
        $response = new SimpleXMLElement($result["response"]);
        $response = __simpleXMLToArray($response);
        $logFile = __DIR__ . '/../../uploads/2018/payment.log';
        $logFile = fopen($logFile, "a");
        $logData = date('Y-m-d h:i:s') . ':  ' . json_encode($response) . "\n\n\n";
        fwrite($logFile, $logData);
        fclose($logFile);


        if (isset($response['ewayTrxnStatus']) && $response['ewayTrxnStatus'] == 'True') {
            $order_data_response['ewayTrxnStatus'] = $response['ewayTrxnStatus'];
            $order_data_response['ewayTrxnNumber'] = $response['ewayTrxnNumber'];
            $order_data_response['ewayAuthCode'] = $response['ewayAuthCode'];
            $order_data_response['ewayTrxnError'] = $response['ewayTrxnError'];
            return $order_data_response;
        } else {
            return false;
        }
    }
    return false;
}

function __makeCurlCall($url, $method = "GET", $headers = null, $gets = null, $posts = null) {
    $ch = curl_init();
    if($gets != null)
    {
        $url.="?".(http_build_query($gets));
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if($posts != null)
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
    }
    if($method == "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
    } else if($method == "PUT") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    } else if($method == "HEAD") {
        curl_setopt($ch, CURLOPT_NOBODY, true);
    }
    if($headers != null && is_array($headers))
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $response = curl_exec($ch);
    $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

    curl_close($ch);
    return array(
        "code" => $code,
        "response" => $response
    );
}

function __simpleXMLToArray(\SimpleXMLElement $xml,$attributesKey=null,$childrenKey=null,$valueKey=null)
{

    if($childrenKey && !is_string($childrenKey)){
        $childrenKey = '@children';
    }
    if($attributesKey && !is_string($attributesKey)){
        $attributesKey = '@attributes';
    }
    if($valueKey && !is_string($valueKey)){
        $valueKey = '@values';
    }

    $return = array();
    $name = $xml->getName();
    $_value = trim((string)$xml);
    if(!strlen($_value)){
        $_value = null;
    };

    if($_value!==null){
        if($valueKey){
            $return[$valueKey] = $_value;
        }
        else{$return = $_value;
        }
    }

    $children = array();
    $first = true;
    foreach($xml->children() as $elementName => $child){
        $value = __simpleXMLToArray($child,$attributesKey, $childrenKey,$valueKey);
        if(isset($children[$elementName])){
            if(is_array($children[$elementName])){
                if($first){
                    $temp = $children[$elementName];
                    unset($children[$elementName]);
                    $children[$elementName][] = $temp;
                    $first=false;
                }
                $children[$elementName][] = $value;
            }else{
                $children[$elementName] = array($children[$elementName],$value);
            }
        }
        else{
            $children[$elementName] = $value;
        }
    }
    if($children){
        if($childrenKey){
            $return[$childrenKey] = $children;
        }
        else{$return = array_merge($return,$children);
        }
    }

    $attributes = array();
    foreach($xml->attributes() as $name=>$value){
        $attributes[$name] = trim($value);
    }
    if($attributes){
        if($attributesKey){
            $return[$attributesKey] = $attributes;
        }
        else{
            if (!is_array($return)) {
                $return = array('returnValue' => $return);
            }
            $return = array_merge($return, $attributes);
        }
    }

    return $return;
}
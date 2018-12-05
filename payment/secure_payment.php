<?php

//securepay
/**
 * check payment info
 * @return bool
 */

$test_url = "https://test.api.securepay.com.au/xmlapi/payment";
$production_url = "https://api.securepay.com.au/xmlapi/payment";
$vendor_name = "ABC0001";
$vendor_password = "abc123";

$pay = array();
$pay['orderTotal'] = 2;
$pay["card_holder"] = "Iman";
$pay["card_number"] = '4444333322221111';
$pay["card_expiry_month"] = '01';
$pay["card_expiry_year"] = '21';
$pay["cvv"] = '123';
$pay["vendor_name"] = $vendor_name;
$pay["vendor_password"] = $vendor_password;
$pay["transaction_mode"] = '';
$pay["test_url"] = $test_url;
$pay["production_url"] = $production_url;
$pay["currency"] = "USD";

$customer = array(
    'email' => 'iman@bitmascot.com',
    'first_name' => 'Iman',
    'last_name' => 'Ali',
    'address_line_1' => '339, 17, lake road',
    'address_line_2' => 'Mohakhali, DOSH, Dhaka',
    'postcode' => '1216'
);

$secure = new securePay();

$result = $secure->checkSecurepayPayment($pay, $customer);

echo '<pre>';
print_r($result);
echo '</pre>';

if ($result && isset($result['securepayTrxnStatusCode']) && ($result['securepayTrxnStatusCode'] == '08' || $result['securepayTrxnStatusCode'] == '00')) {
    $response_details = explode(',', $result['securepayTrxnError']);
    $payment_response_code = $response_details[0];
    $payment_response_text = $response_details[1];

    $payment_reference_number = $result['securepayTrxnNumber'];
    $securepay_order_id = $result['securepayOrderId'];
    $securepayTrxnStatus = $result['securepayTrxnStatus'];
    $securepayTrxnStatusCode = $result['securepayTrxnStatusCode'];
}

class securePay
{

    public function checkSecurepayPayment($pay, $customer)
    {

        $price = $pay['orderTotal'];

        if ($pay['transaction_mode']) { //live mode
            $curlUrl = $pay['production_url'];
            $merchantID = $pay['vendor_name'];
            $merchantPassword = $pay['vendor_password'];

        } else { //test mode
            $curlUrl = $pay['test_url'];
            $merchantID = $pay['vendor_name'];
            $merchantPassword = $pay['vendor_password'];
        }

        $TotalAmount = $price * 100; // $price; /* 1$ = 100 cent */
        $msgId = $this->_GetMessageId();
        $date = new DateTime();
        $CustomerEmail = trim($customer['email']);

        $CustomerFirstName = trim($customer['first_name']);
        $CustomerLastName = trim($customer['last_name']);
        $CustomerAddress = trim($customer['address_line_1']);
        $Customerpostcode = trim($customer['postcode']);

        if (isset($customer['address_line_2']) && (strlen($customer['address_line_2']) > 0)) {
            $CustomerAddress .= ' ' . $customer['address_line_2'];
        }
        $CardHoldersName = trim($pay['card_holder']);
        $CardNumber = trim($pay['card_number']);
        $CardExpiryMonth = trim($pay['card_expiry_month']);
        $CardExpiryYear = trim($pay['card_expiry_year']);
        $CVV = trim($pay['cvv']);
        $securepay_order_id = time();


        if ($this->valid($CardExpiryMonth, $CardExpiryYear)) {

            $directXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
                "<SecurePayMessage>" .
                "<MessageInfo>" .
                "<messageID>" . $msgId . "</messageID>" .
                "<messageTimestamp>" . $date->getTimestamp() . "</messageTimestamp>" .
                "<timeoutValue>60</timeoutValue>" .
                "<apiVersion>xml-4.2</apiVersion>" .
                "</MessageInfo>" .
                "<MerchantInfo>" .
                "<merchantID>" . $merchantID . "</merchantID>" .
                "<password>" . $merchantPassword . "</password>" .
                "</MerchantInfo>" .
                "<RequestType>Payment</RequestType>" .

                "<Payment>" .
                "<TxnList count='1'>" .
                "<Txn ID='1'>" .
                "<txnType>0</txnType>" .
                "<txnSource>23</txnSource>" .
                "<amount>" . $TotalAmount . "</amount>" .
                "<recurring>No</recurring>" .
                "<currency>" . $pay['currency'] . "</currency>" .
                "<purchaseOrderNo>" . $securepay_order_id . "</purchaseOrderNo>" .
                "<CreditCardInfo>" .
                "<cardHolderName>" . $CardHoldersName . "</cardHolderName>" .
                "<cardNumber>" . $CardNumber . "</cardNumber>" .
                "<expiryDate>" . $CardExpiryMonth . "/" . $CardExpiryYear . "</expiryDate>" .
                "<cvv>" . $CVV . "</cvv>" .
                "</CreditCardInfo>" .
                "</Txn>" .
                "</TxnList>" .
                "</Payment>" .
                "<BuyerInfo>" .
                "<firstName>" . $CustomerFirstName . "</firstName>" .
                "<lastName>" . $CustomerLastName . "</lastName>" .
                "<emailAddress>" . $CustomerEmail . "</emailAddress>" .
                "<zipcode>" . $Customerpostcode . "</zipcode>" .
                "<zipcode>" . $Customerpostcode . "</zipcode>" .
                "</BuyerInfo>" .
                "</SecurePayMessage>";


            ini_set('max_execution_time', 0);
            $result = $this->__makeCurlCall(
                $curlUrl, /* CURL URL */
                "POST", /* CURL CALL METHOD */
                array( /* CURL HEADERS */
                    "Content-Type: text/xml; charset=utf-8",

                ),
                null, /* CURL GET PARAMETERS */
                $directXML /* CURL POST PARAMETERS AS XML */
            );

            if ($result != null && isset($result["response"])) {
                $response = new SimpleXMLElement($result["response"]);
                $response = $this->__simpleXMLToArray($response);
//                echo '<pre>';
//                print_r($response);
//                echo '</pre>';
                unset($response['Payment']['TxnList']['Txn']['CreditCardInfo']);
                $this->log($response, 'payment');
                if (isset($response['Payment']['TxnList']['Txn']['responseCode']) && ($response['Payment']['TxnList']['Txn']['responseCode'] == '08' || $response['Payment']['TxnList']['Txn']['responseCode'] == '00')) {

                    $order_data_response['securepayTrxnStatus'] = $response['Payment']['TxnList']['Txn']['responseText'];
                    $order_data_response['securepayTrxnStatusCode'] = $response['Payment']['TxnList']['Txn']['responseCode'];
                    $order_data_response['securepayTrxnNumber'] = $response['Payment']['TxnList']['Txn']['txnID'];
                    $order_data_response['securepayTrxnError'] = $response['Payment']['TxnList']['Txn']['responseCode'] . ',' . $response['Payment']['TxnList']['Txn']['responseText'];
                    $order_data_response['securepayOrderId'] = $response['Payment']['TxnList']['Txn']['purchaseOrderNo'];
                    return $order_data_response;
                } else {
                    return false;
                }
            }

        }

        return false;
    }

    function __makeCurlCall($url, $method = "GET", $headers = null, $gets = null, $posts = null)
    {
        $ch = curl_init();
        if ($gets != null) {
            $url .= "?" . (http_build_query($gets));
        }
        $this->log($url, 'curl_log');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($posts != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
        }
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
        } else if ($method == "PUT") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        } else if ($method == "HEAD") {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }
        if ($headers != null && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //$this->log($response,'curl_log');
        $this->log($code, 'curl_log');
        curl_close($ch);
        return array(
            "code" => $code,
            "response" => $response
        );
    }

    public function __simpleXMLToArray(\SimpleXMLElement $xml, $attributesKey = null, $childrenKey = null, $valueKey = null)
    {

        if ($childrenKey && !is_string($childrenKey)) {
            $childrenKey = '@children';
        }
        if ($attributesKey && !is_string($attributesKey)) {
            $attributesKey = '@attributes';
        }
        if ($valueKey && !is_string($valueKey)) {
            $valueKey = '@values';
        }

        $return = array();
        $name = $xml->getName();
        $_value = trim((string)$xml);
        if (!strlen($_value)) {
            $_value = null;
        };

        if ($_value !== null) {
            if ($valueKey) {
                $return[$valueKey] = $_value;
            } else {
                $return = $_value;
            }
        }

        $children = array();
        $first = true;
        foreach ($xml->children() as $elementName => $child) {
            $value = $this->__simpleXMLToArray($child, $attributesKey, $childrenKey, $valueKey);
            if (isset($children[$elementName])) {
                if (is_array($children[$elementName])) {
                    if ($first) {
                        $temp = $children[$elementName];
                        unset($children[$elementName]);
                        $children[$elementName][] = $temp;
                        $first = false;
                    }
                    $children[$elementName][] = $value;
                } else {
                    $children[$elementName] = array($children[$elementName], $value);
                }
            } else {
                $children[$elementName] = $value;
            }
        }
        if ($children) {
            if ($childrenKey) {
                $return[$childrenKey] = $children;
            } else {
                $return = array_merge($return, $children);
            }
        }

        $attributes = array();
        foreach ($xml->attributes() as $name => $value) {
            $attributes[$name] = trim($value);
        }
        if ($attributes) {
            if ($attributesKey) {
                $return[$attributesKey] = $attributes;
            } else {
                if (!is_array($return)) {
                    $return = array('returnValue' => $return);
                }
                $return = array_merge($return, $attributes);
            }
        }

        return $return;
    }

    /**
     * Generates a new message ID
     * @return string A string of 30 random hex characters
     */
    function _GetMessageId()
    {
        $code = '';

        foreach (range(1, 30) as $offset)
            $code .= dechex(rand(0, 15));
        return $code;
    }

    function valid($CardExpiryMonth, $CardExpiryYear)
    {

        $expireDate = $CardExpiryMonth . '/' . $CardExpiryYear;
        return (
        $this->ValidExpiryDate($expireDate)

        );
    }

    /**
     * Validates an expiry date and ensures that it confirms to the SecurePay standards
     * @param string $ExpiryDate Optional expiry date to test. If none is specified the objects expiry date is used instead
     * @return bool TRUE if the expiry date passes validation
     */
    function ValidExpiryDate($ExpiryDate = null)
    {
        $test_expiry = ($ExpiryDate) ? $ExpiryDate : $this->ExpiryDate;
        if (preg_match('!([0-9]{1,2})/([0-9]{2,4})!', $test_expiry, $matches)) {
            if (strlen($matches[1]) == 1)
                $matches[1] = "0{$matches[1]}";
            if (strlen($matches[2]) == 4)
                $matches[2] = substr($matches[2], -2);
            $this->ExpiryDate = "{$matches[1]}/{$matches[2]}";

            return (($matches[1] > 0) && ($matches[1] < 13) && ($matches[2] >= date('y')) && ($matches[2] < date('y') + 30)); // Check that month and years are valid
        } else {
            $this->Error = 'Invalid Expiry Date';
            return FALSE; // Failed RegExp checks
        }
    }

    function log($log, $file_name = '')
    {
        if (empty($file_name)) {
            $file_name = 'debug';
        }

        $file_name = $file_name . '.log';
        $folder = 'logs';

        if(!file_exists('logs')){
            mkdir($folder, 0755);
        }

        $file_path = $folder.'/' . $file_name;

        if (is_array($log) || is_object($log)) {
            $log_data = print_r($log, true);
        } else {
            $log_data = $log;
        }

        $log_data = date('Y-m-d H:i:s') . " Debug: \n" . $log_data."\n\n";

        error_log($log_data, 3, $file_path);
    }

}
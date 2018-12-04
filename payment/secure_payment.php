<?php

//securepay
/**
 * check payment info
 * @return bool
 */

$pay = array();
$pay['orderTotal']                  = $this->Session->read('Order.info.total');
$pay["card_holder"]                 = $this->Session->read('Order.payment.securepay.ccname');
$pay["card_number"]                 = $this->Session->read('Order.payment.securepay.ccnumber');
$pay["card_expiry_month"]           = $this->Session->read('Order.payment.securepay.month');
$pay["card_expiry_year"]            = $this->Session->read('Order.payment.securepay.year');
$pay["cvv"]                         = $this->Session->read('Order.payment.securepay.cvnumber');
$pay["vendor_name"]                 = $this->Session->read('Order.payment.securepay.vendor_name');
$pay["vendor_password"]             = $this->Session->read('Order.payment.securepay.vendor_password');
$pay["transaction_mode"]            = $this->Session->read('Order.payment.securepay.transaction_mode');
$pay["test_url"]                    = $this->Session->read('Order.payment.securepay.test_url');
$pay["production_url"]              = $this->Session->read('Order.payment.securepay.production_url');
$pay["production_url"]              = $this->Session->read('Order.payment.securepay.production_url');
$pay["currency"]                    = $this->Session->read('Order.currency_info.currency_code');

$payment_date = array();
$customer = array(

);

$secure = new securePay();

$payment_info = $secure->checkSecurepayPayment($payment_date, $customer);

echo '<pre>';
print_r($payment_info);
echo '</pre>';

class securePay
{

    public function checkSecurepayPayment($pay, $customer)
    {

        $price = $pay['orderTotal'];
        $this->log($customer, 'customer');

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

    /**
     * Generates a new message ID
     * @return string A string of 30 random hex characters
     */
    function _GetMessageId() {
        $code = '';

        foreach (range(1,30) as $offset)
            $code .= dechex(rand(0,15));
        return $code;
    }

    function valid($CardExpiryMonth,$CardExpiryYear) {

        $expireDate= $CardExpiryMonth.'/'.$CardExpiryYear;
        return (
        $this->ValidExpiryDate($expireDate)

        );
    }

    /**
     * Validates an expiry date and ensures that it confirms to the SecurePay standards
     * @param string $ExpiryDate Optional expiry date to test. If none is specified the objects expiry date is used instead
     * @return bool TRUE if the expiry date passes validation
     */
    function ValidExpiryDate($ExpiryDate = null) {
        $test_expiry = ($ExpiryDate) ? $ExpiryDate : $this->ExpiryDate;
        if (preg_match('!([0-9]{1,2})/([0-9]{2,4})!',$test_expiry, $matches)) {
            if (strlen($matches[1]) == 1)
                $matches[1] = "0{$matches[1]}";
            if (strlen($matches[2]) == 4)
                $matches[2] = substr($matches[2],-2);
            $this->ExpiryDate = "{$matches[1]}/{$matches[2]}";

            return ( ($matches[1] > 0) && ($matches[1] < 13) && ($matches[2] >= date('y')) && ($matches[2] < date('y') + 30) ); // Check that month and years are valid
        } else {
            $this->Error = 'Invalid Expiry Date';
            return FALSE; // Failed RegExp checks
        }
    }

}
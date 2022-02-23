<?php
//namespace App\payment\securepay;
//use App\DotEnv;
include "./../../DotEnv.php";

class SecurePayHandler
{
    public function __construct()
    {
        (new DotEnv(__DIR__ . '/.env'))->load();
    }

    public function getAccessSecurePayToken()
    {
        $securepay_auth_url = "ttps://welcome.api2.sandbox.auspost.com.au/oauth/token";
        $contentType = "application/x-www-form-urlencoded";
        $s_data = [
            'grant_type' => 'client_credentials',
            'audience' => 'https://api.payments.auspost.com.au'
        ];

        $s_data = $this->getPostFieldString($s_data);
        $clientId = getenv('SECURE_PAY_CLIENT_ID');
        $clientSecret = getenv('SECURE_PAY_CLIENT_SECRET');
        $header = [];
        $header[] = 'Authorization: Basic '.base64_encode($clientId.':'.$clientSecret);
        $header[] = "Content-Type: ".$contentType;

        $res = $this->processViaCurl($securepay_auth_url, [],$s_data, 'post', $contentType, $header);
        return $res;
    }

    public function securepayMakePaymentByToken($data)
    {
        $sauth = $this->getAccessSecurePayToken();

        if (empty($sauth['access_token'])) {
            $message = 'Fail to authenticate merchant account credentials. Please contact to admin.';
            throw new $message;
        }

        $data = $_POST;
        $pay = $this->securepayDoPaymentByToken($sauth, $data);
        echo "<pre>";
        print_r($pay);
        echo "</pre>";
        die("xxx");

//        $pay['status'] = $pay['status'] ?? '';
//        $pay['gatewayResponseCode'] = $pay['gatewayResponseCode'] ?? '';
//
//        if(!empty($pay['errors'])){
//            $message = $pay['errors'][0]['detail'].".";
//            $this->payPaymentError($message,'secure_pay');
//        }
//
//        if(strtolower($pay['status']) === 'failed' && !empty($pay['gatewayResponseCode'])
//            && !in_array($pay['gatewayResponseCode'], ["00" ,"11" ,"77" ,"08" ,"16"])){
//            $message = $pay['gatewayResponseMessage'].".";
//
//            $this->payPaymentError($message,'secure_pay');
//        }
//
//
//        if (!empty($pay) && strtolower($pay['status']) == 'paid' && $pay['amount'] > 0) {
//            $payment = $this->getDbTable('SecurepayPayments')->newEntity();
//            $payment->integration_method = 'js_sdk';
//            $payment->payment_mode = $paymentInfo['Transection_Mode'];
//            $payment->refid = $pay['orderId'] ?? '';
//            $payment->createdAt = $pay['createdAt'] ?? '';
//            $payment->merchantCode = $pay['merchantCode'] ?? '';
//            $payment->customerCode = $pay['customerCode'] ?? '';
//            $payment->amount = $pay['amount'] / 100;
//            $payment->status = $pay['status'] ?? '';
//            $payment->bankTransactionId = $pay['bankTransactionId'] ?? '';
//            $payment->gatewayResponseCode = $pay['gatewayResponseCode'] ?? '';
//            $payment->gatewayResponseMessage = $pay['gatewayResponseMessage'] ?? '';
//
//            $payment->txnid = $pay['bankTransactionId']??'';
//            $payment->restext = $pay['gatewayResponseMessage']??'';
//            $payment->merchant = $pay['merchantCode']??'';
//            $payment->rescode = $pay['gatewayResponseCode']??'';
//            $payment->fingerprint = $pay['bankTransactionId']??'';
//            $payment->timestamp = $pay['timestamp']??time();
//            $payment->currency = $pay['currency']??'AUD';
//
//            $payment->summarycode = $pay['summarycode']??'';
//            $payment->suramount = $pay['suramount']??'';
//            $payment->settdate = $pay['settdate']??'';
//            $payment->surfee = $pay['surfee']??'';
//            $payment->cardtype = $pay['cardtype']??'';
//            $payment->surrate = $pay['surrate']??'';
//
//            $payment = $this->getDbTable('SecurepayPayments')->save($payment);
//
//            $this->updateSecurePayInCart($paymentInfo, $payment);
//
//        } else {
//            $message = 'Payment Failed';
//            $this->payPaymentError($message,'secure_pay');
//        }

    }

     private function securepayDoPaymentByToken($sauth, $data, $paymentInfo)
    {

        $path = "secure_js_sdk";
        $file = "do_payment_".date('Ymd');

        $payment_mode = strtolower(getenv('PAYMENT_MODE'));
        if(!in_array(strtolower($payment_mode), ['live','production'])){
            $payment_mode = 'test';
        } else {
            $payment_mode = 'live';
        }
        $securepay_payment_url = getenv('SECURE_PAY_REST_API_URL');

        $contentType = "application/json";
        $merchantCode = getenv('SECURE_PAY_MERCHANT_CODE');

        $s_data = [
            'amount' => $data['amount']??'',
            'merchantCode' => $merchantCode,
            'token' => $data['token']??'',
            'ip' => $this->get_ip_address(),
            'orderId' => $data['primary_ref']??''
        ];


        $s_data1 = $s_data;
        $s_data1['token'] = "*********";

        $header = [];
        $header[] = 'Authorization:Bearer '.$sauth['access_token']??'';
        $header[] = "Content-Type:".$contentType;
        $header[] = "tls:TLSv1.2";

        $res = $this->processViaCurl($securepay_payment_url,[], $s_data, 'post', $contentType, $header);

        return $res;
    }

    function processViaCurl($url,$query_param=[], $data=array(),$method="post", $contentType="application/x-www-form-urlencoded", $header=[], $debug=false )
    {
        if(empty($contentType)){
            $contentType="application/x-www-form-urlencoded";
        }

        if(!empty($query_param)){
            $url1 = explode('?',$url);
            $url = array_shift($url1);
            $url1 = implode('?', $url1);

            parse_str($url1??'', $query);
            $query = array_merge($query, (array)$query_param);
            $url1 = $url.'?'.$this->getPostFieldString($query);

            $url = $url1;
        }

        $ssh_mode = FALSE;

        $ch = curl_init();
        if(empty($data['_method'])){
            if(strtolower($method)=="post"){
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
            }
            else if(strtolower($method)=="delete"){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }
            else if(strtolower($method)=="put"){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // note the PUT here
            }
            else if(strtolower($method)=="get"){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            }
            else{
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            }
        }


        if($contentType=='application/json'){
            $data1 = json_encode($data);
        } else {
            $data1 = $this->getPostFieldString($data);
        }

        if(empty($header)){
            $header[] = "Content-Type: ".$contentType;
            if(!isset($data['access_token'])){
                $header[] = 'Authorization: FALSE';
            }
            else{
                $header[] = 'Authorization: Bearer '.$data['access_token'];
            }
        }

        $header[] = 'Content-length: '.strlen($data1);


        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $ssh_mode );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data1 );
        $result = curl_exec($ch);

        curl_close($ch);

        if( json_decode($result) && json_last_error() === JSON_ERROR_NONE){
            $result = json_decode($result,true);
        }


        if($debug){
            echo "<div  style='max-width:80%; margin-left: 280px;max-height:320px;'>";
            echo "<pre style='width:100%;max-height:300px;overflow-y: auto; '>";

            echo "API URL : ".$url."\n";
            echo "\nPost Value : \n";
            echo "----------------------------------\n";
            echo print_r($data,true);
            echo "\nPost Value STR : \n";
            echo "----------------------------------\n";
            echo $data1;

            echo "\n\nResponse : \n";
            echo "----------------------------------\n";
            var_dump(self::htmlentities($result));

            echo "</pre>";
            echo "</div>";
            echo "<div style='clear: both'></div>";
            die;
        }

        return $result;
    }

    function getPostFieldString($data){
        if(!is_array($data)){
            return $data;
        }
//        return json_encode($data);
        $data = $this->prepareData($data);
        if(empty($data) || !is_array($data)){
            return "";
        }

        $fields_string = "";
        foreach($data as $key => $value){
            if(empty($value)){
                $data[$key] = "";
            }
            else{
                $data[$key] = urlencode($value);
            }
            $fields_string .= $key . "=" . $data[$key] . "&";

        }

        $fields_string = rtrim($fields_string,"&");

        return $fields_string;

    }

    function prepareData($array,$prepend="")
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                if($prepend==""){
                    $results = array_merge($results, $this->prepareData($value, $key));
                }
                else{
                    $results = array_merge($results, $this->prepareData($value, $prepend."_".urlencode($key)));
                }

            } else {
                if($prepend==""){
                    $results[urlencode($key)] = $value;
                }
                else{
                    $results[$prepend."_".urlencode($key)] = $value;
                }
            }
        }

        return $results;
    }

    function get_ip_address()
    {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    // trim for safety measures
                    $ip = trim($ip);
                    // attempt to validate IP
                    if ($this->validate_ip($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
    }

    function validate_ip($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }
}
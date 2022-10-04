<?php

namespace App\ApiService;

use App\Traits\WebaliveLog;
use GuzzleHttp;
use GuzzleHttp\Client;

class ApiRequestHandler
{
    use WebaliveLog;

    public $data;
    public $subject;
    public $api_url;
    public $api_username;
    public $api_password;

    /**
     * Create a new request instance.
     *
     * @return void
     */
    public function __construct() {
        $this->api_url = config('constants.axiel_api_url');
        $this->api_username = config('constants.axiel_api_username');
        $this->api_password = config('constants.axiel_api_password');
    }

    public function __makeCurlCall($url, $method = "GET", $headers = null, $gets = null, $posts = null) {
        $ch = curl_init();
        if($gets != null)
        {
            $url.="?".(http_build_query($gets));
        }
        //$this->log($url,'curl_log');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->api_username.":".$this->api_password); //Your credentials goes here
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
        //$this->log($response,'curl_log');
        //$this->log($code,'curl_log');
        curl_close($ch);
        return array(
            "code" => $code,
            "response" => $response
        );
    }

    public function __simpleXMLToArray(\SimpleXMLElement $xml,$attributesKey=null,$childrenKey=null,$valueKey=null)
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
            $value = $this->__simpleXMLToArray($child,$attributesKey, $childrenKey,$valueKey);
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

    public function _getCustomer($customerEmail, $data) {
        $url = $this->api_url."?database=people&search=C8=" . $customerEmail;

        $log_filename = $data['log_filename'];
        $log_filename_dev = $data['log_filename'].'_dev';
        $log_path = $data['log_path'];
        $this->waLog('===========Customer search request url============', $log_filename, $log_path);
        $this->waLog($url, $log_filename, $log_path);

        $result = $this->__makeCurlCall(
            $url,
            "GET",
            null,
            null /* CURL GET PARAMETERS */
        );

        $return_data = array(
            'priref' => '',
            'code'=> $result["code"],
            'response'=> '',
        );
        if(!empty($result["response"])) {
            $response = new \SimpleXMLElement($result["response"]);
            $response = $this->__simpleXMLToArray($response);
            $return_data['response'] = $response;
            $this->waLog('===========Customer search response============', $log_filename_dev, $log_path);
            $this->waLog($response, $log_filename_dev, $log_path);

            if(isset($response['diagnostic'])) {
                if(isset($response['diagnostic']['hits']) && $response['diagnostic']['hits'] == 1) {
                    if(isset($response['recordList']['record']['priref'])) {
                        $return_data['priref'] = $response['recordList']['record']['priref'];
                    }
                }
            }
        }
        if (empty($return_data['priref'])) {
            $this->waLog('Customer priref not found by customer search email='.$customerEmail, $log_filename, $log_path);
        }
        return $return_data;
    }

    public function _getCustomerByReaderTicketNo($readerTicketNo) {
        $url = $this->api_url."?database=people&search=3c=\"" . $readerTicketNo."\"";
        $result = $this->__makeCurlCall(
            $url,
            "GET",
            null,
            null /* CURL GET PARAMETERS */
        );

        $this->waLog('request_url', 'reading');
        $this->waLog($url, 'reading');
        $return_data = array(
            'priref' => '',
            'surname' => '',
            'contact_ticket_no' => '',
            'code'=> $result["code"],
            'response'=> $result["response"],
        );
        if(!empty($result["response"])) {
            $response = new \SimpleXMLElement($result["response"]);
            $response = $this->__simpleXMLToArray($response);
            $return_data['response'] =  $response;

            if(isset($response['diagnostic'])) {
                if(isset($response['diagnostic']['hits']) && $response['diagnostic']['hits'] == 1) {
                    if(isset($response['recordList']['record']) && !empty($response['recordList']['record'])) {
                        $record = $response['recordList']['record'];
                        $surname = isset($record['surname']) ? $record['surname']: '';
                        $return_data['priref'] =  $record['priref'];
                        $return_data['surname'] = $surname;
                        $return_data['contact_ticket_no'] = $record['contact_ticket_no'];
                    }
                }
            }
        }
        return $return_data;
    }


    public function _createCustomer($data) {

        $log_filename = $data['log_filename'];
        $log_filename_dev = $data['log_filename'].'_dev';
        $log_path = $data['log_path'];

        $url = $this->api_url;

        $queryData = [
            'database' => 'people',
            'command' => 'insertrecord'
        ];
        $customerEmail = $data['email'];
        $name = $data['surname']." ".$data['forename'];
        $forename = $data['forename'];
        $surname = $data['surname'];
        $addressLine1 = $data['addressLine1'];
        $addressLine2 = $data['addressLine2'];
        $suburb = $data['suburb'];
        $state = $data['state'];
        $postcode = $data['postcode'];
        $country = $data['country'];
        $mobile = $data['mobile'];

        $customerEmail = htmlspecialchars($customerEmail,ENT_XML1,'UTF-8');
        $name = htmlspecialchars($name,ENT_XML1,'UTF-8');
        $forename = htmlspecialchars($forename,ENT_XML1,'UTF-8');
        $surname = htmlspecialchars($surname,ENT_XML1,'UTF-8');
        $addressLine1 = htmlspecialchars($addressLine1,ENT_XML1,'UTF-8');
        $addressLine2 = htmlspecialchars($addressLine2,ENT_XML1,'UTF-8');
        $suburb = htmlspecialchars($suburb,ENT_XML1,'UTF-8');
        $state = htmlspecialchars($state,ENT_XML1,'UTF-8');
        $postcode = htmlspecialchars($postcode,ENT_XML1,'UTF-8');
        $country = htmlspecialchars($country,ENT_XML1,'UTF-8');
        $mobile = htmlspecialchars($mobile,ENT_XML1,'UTF-8');

        $directXML = "<adlibXML>
                    <recordList>
                        <record priref='0'>
                            <name>".$name."</name>
                            <forename>".$forename."</forename>
                            <surname>".$surname."</surname>
                            <delivery.address.line.one>".$addressLine1."</delivery.address.line.one>
                            <delivery.address.line.two>".$addressLine2."</delivery.address.line.two>
                            <delivery.suburb>".$suburb."</delivery.suburb>
                            <delivery.state>".$state."</delivery.state>
                            <delivery.postcode>".$postcode."</delivery.postcode>
                            <delivery.county>".$country."</delivery.county>
                            <home.e-mail>".$customerEmail."</home.e-mail>
                            <home.mobile>".$mobile."</home.mobile>
                            <name.type>CONTACTS</name.type>
                        </record>
                    </recordList>
                </adlibXML>";

        $this->waLog('===========Customer create request============', $log_filename, $log_path);
        $this->waLog($directXML, $log_filename, $log_path);

        $result = $this->__makeCurlCall(
            $url,
            "POST",
            array( /* CURL HEADERS */
                "Content-Type: text/xml; charset=utf-8",
                "Accept: text/xml",
                "Pragma: no-cache",
                "Content_length: ".strlen(trim($directXML))
            ),
            $queryData, /* CURL GET PARAMETERS */
            $directXML /* CURL POST PARAMETERS AS XML */
        );

        $return_data = array(
            'priref' => '',
            'code'=> $result["code"],
            'response'=> $result["response"],
        );
        if(!empty($result["response"])) {
            $response = new \SimpleXMLElement($result["response"]);
            $response = $this->__simpleXMLToArray($response);
            $return_data['response'] = $response;
            $this->waLog('===========Customer create response============', $log_filename_dev, $log_path);
            $this->waLog($response, $log_filename_dev, $log_path);

            if(isset($response['diagnostic'])) {
                if(isset($response['diagnostic']['hits']) && $response['diagnostic']['hits'] == 1) {
                    if(isset($response['recordList']['record']['priref'])) {
                        $return_data['priref'] =  $response['recordList']['record']['priref'];
                    }
                }
            }
        }
        return $return_data;
    }

    function _makeReadingRoomRequest($customerPriref, $post_data) {

        $url = $this->api_url;

        $queryData = [
            'database' => 'workflow',
            'command' => 'insertrecord'
        ];

        $log_filename = $post_data['log_filename'];
        $log_path = $post_data['log_path'];

        $today = date('Y-m-d');
        $time = '09:00';

        $object_xml = '';
        $request_details_xml = '';
        if(isset($post_data['cart_data'])){
            $cart_data = $post_data['cart_data'];

            $this->waLog('===========Cart data============', $log_filename, $log_path);
            $this->waLog($cart_data, $log_filename, $log_path);

            // Request item
            if(isset($cart_data['request_items']) && !empty($cart_data['request_items'])) {
                foreach ($cart_data['request_items'] as $item) {

                    if(isset($item['object_number']) && !empty($item['object_number']) && $item['object_number'] !='null'){
                        $object_number = $item['object_number'];
                    }else{
                        $object_number = '----[0000]';
                    }

                    if($item['inx_number'] == 'null'){
                        $item['inx_number'] = '';
                    }

                    $object_xml .= "<api_object_number>" . $object_number . "</api_object_number>
                              <api_object_note>". $item['inx_number'] . htmlspecialchars($item['item_title'],ENT_XML1,'UTF-8') . "</api_object_note>";
                }
            }

            // Request item
            if(isset($cart_data['new_request_items']) && !empty($cart_data['new_request_items'])) {
                foreach ($cart_data['new_request_items'] as $item) {
                    $object_number = '----[0000]';
                    $object_xml .= "<api_object_number>" . $object_number . "</api_object_number>";
                    $object_xml .= "<api_object_note>";
                    // $object_xml .= htmlspecialchars($item['series_no'],ENT_XML1,'UTF-8');
                    // $object_xml .= htmlspecialchars($item['series_name'],ENT_XML1,'UTF-8');
                    // $object_xml .= htmlspecialchars($item['container_no'],ENT_XML1,'UTF-8');
                    // $object_xml .= htmlspecialchars($item['item_no'],ENT_XML1,'UTF-8');
                    // $object_xml .= htmlspecialchars($item['item_name'],ENT_XML1,'UTF-8');
                    if (isset($item['series_no']) && isset($item['series_name'])){
                        $object_xml .= "Series: " . htmlspecialchars($item['series_no'],ENT_XML1,'UTF-8') . " - " .
                            htmlspecialchars($item['series_name'],ENT_XML1,'UTF-8') . " | ";
                    } elseif (isset($item['series_no'])) {
                        $object_xml .= "Series: " . htmlspecialchars($item['series_no'],ENT_XML1,'UTF-8') . " | ";
                    } elseif (isset($item['series_name'])) {
                        $object_xml .= "Series: " . htmlspecialchars($item['series_name'],ENT_XML1,'UTF-8') . " | ";
                    }
                    if(isset($item['container_no'])) {
                        $object_xml .= "Container: " . htmlspecialchars($item['container_no'],ENT_XML1,'UTF-8') . " | ";
                    }
                    if(isset($item['item_no'])) {
                        $object_xml .= "Item: " . htmlspecialchars($item['item_no'],ENT_XML1,'UTF-8') . " | ";
                    }
                    if(isset($item['item_name'])) {
                        $object_xml .= "Name: " . htmlspecialchars($item['item_name'],ENT_XML1,'UTF-8') . " | ";
                    }
                    $object_xml .= "</api_object_note>";
                }
            }
        }

        $directXML = "<adlibXML>
                          <recordList>
                            <record priref='0'>
                              <description>Reading room request</description>
                              <completion.date>".$post_data['completion_date']."</completion.date>
                              <completion.time>".$time."</completion.time>
                              <request.details>Primo Job: ".$log_filename."</request.details>
                              <client.name.lref>".$customerPriref."</client.name.lref>
                              <dataType>FolderData</dataType>
                              <request.date.received>".$today."</request.date.received>
                              <request.from.name></request.from.name>
                              <status>Started</status>
                              <recordType>Folder</recordType>
                              ".$object_xml."
                              <assigned_to></assigned_to>
                              <priority></priority>
                              <topNode>x</topNode>
                              <notes></notes>
                              <job_type>Reading room request</job_type>
                              <end.date_predicted>".$post_data['completion_date']."</end.date_predicted>
                            </record>
                          </recordList>
                        </adlibXML>";

        $this->waLog('===========Request URL============', $log_filename, $log_path);
        $this->waLog($url, $log_filename, $log_path);

        $this->waLog('===========Request for reading room============', $log_filename, $log_path);
        $this->waLog($directXML, $log_filename, $log_path);

        $result = $this->__makeCurlCall(
            $url,
            "POST",
            array( /* CURL HEADERS */
                "Content-Type: text/xml; charset=utf-8",
                "Accept: text/xml",
                "Pragma: no-cache",
                "Content_length: ".strlen(trim($directXML))
            ),
            $queryData, /* CURL GET PARAMETERS */
            $directXML /* CURL POST PARAMETERS AS XML */
        );

        $job_number = '';
        if($result != null && isset($result["response"]) && !empty($result["response"])) {
            $response = new \SimpleXMLElement($result["response"]);
            $response = $this->__simpleXMLToArray($response);
            $this->waLog('===========Response of reading room============', $log_filename, $log_path);
            $this->waLog($response, $log_filename, $log_path);

            if(isset($response['diagnostic'])) {
                if(isset($response['diagnostic']['hits']) && $response['diagnostic']['hits'] == 1) {
                    if(isset($response['recordList']['record']['jobnumber'])) {
                        $job_number = $response['recordList']['record']['jobnumber'];
                    }
                }
            }
        }
        return $job_number;
    }

    function _copyOrderProcess($customerPriref, $post_data) {

        $url = $this->api_url;

        $queryData = [
            'database' => 'workflow',
            'command' => 'insertrecord'
        ];

        $log_filename = $post_data['log_filename'];
        $log_filename_dev = $post_data['log_filename']."_dev";
        $log_path = $post_data['log_path'];
        $this->waLog('===========copyOrderProcess post data============', $log_filename_dev, $log_path);
        $this->waLog($post_data, $log_filename_dev, $log_path);

        $object_xml = '';
        $request_details_xml = '';
        if(isset($post_data['cart_data'])){
            $cart_data = $post_data['cart_data'];

            foreach ($cart_data['cart_items'] as $item){

                $api_object_type = '';
                if(isset($item['is_digital_copy']) && $item['is_digital_copy']){
                    $api_object_type = 'Digital';
                }
                if(isset($item['is_hard_copy']) && $item['is_hard_copy']){
                    $api_object_type = 'HardCopy';
                }
                $item_title = $item['item_title'];
                if($item['product_type'] == 'image' && !empty($item['image_title'])) {
                    $item_title = $item['image_title'];
                }

                if(isset($item['object_number']) && !empty($item['object_number']) && $item['object_number'] !='null'){
                    $object_number = $item['object_number'];
                }else{
                    $object_number = '----[0000]';
                }
                if($object_number == 'null'){
                    $object_number = '----[0000]';
                }

                $object_xml .="

                              <api_object_number>".htmlspecialchars($object_number,ENT_XML1,'UTF-8')."</api_object_number>
                              <api_object_qty>".$item['quantity']."</api_object_qty>
                              <api_object_type>".htmlspecialchars($api_object_type,ENT_XML1,'UTF-8')."</api_object_type>
                              <api_object_note>".htmlspecialchars($item_title,ENT_XML1,'UTF-8')."</api_object_note>

                              ";

            }
        }

        $post_data['completion_date'] = htmlspecialchars($post_data['completion_date'],ENT_XML1,'UTF-8');
        $today = date('Y-m-d');
        $time = '17:00';

        $directXML = "<adlibXML>
                          <recordList>
                            <record priref='0'>
                              <description>Copy order</description>
                              <completion.date>".$post_data['completion_date']."</completion.date>
							  <primo_order_number>Primo Job: ".$log_filename."</primo_order_number>
                              <completion.time>".$time."</completion.time>
                              <request.details>Primo Job: ".$log_filename."</request.details>
                              <client.name.lref>".$customerPriref."</client.name.lref>
                              <dataType>FolderData</dataType>
                              <request.date.received>".$today."</request.date.received>
                              <request.from.name></request.from.name>
                              <status>Started</status>
                              <recordType>Folder</recordType>
                              ".$object_xml."
                              <assigned_to></assigned_to>
                              <priority></priority>
                              <topNode>x</topNode>
                              <notes></notes>
                              <job_type>copy_order</job_type>
                              <end.date_predicted>".$post_data['completion_date']."</end.date_predicted>
                            </record>
                          </recordList>
                        </adlibXML>";

        $this->waLog('===========XML Request for copy order============', $log_filename, $log_path);
        $this->waLog($directXML, $log_filename, $log_path);

        $result = $this->__makeCurlCall(
            $url,
            "POST",
            array( /* CURL HEADERS */
                "Content-Type: text/xml; charset=utf-8",
                "Accept: text/xml",
                "Pragma: no-cache",
                "Content_length: ".strlen(trim($directXML))
            ),
            $queryData, /* CURL GET PARAMETERS */
            $directXML /* CURL POST PARAMETERS AS XML */
        );
        $return_data = array(
            'job_number' => '',
            'code'=> $result["code"],
            'response'=> '',
        );
        if(!empty($result["response"])) {
            $response = new \SimpleXMLElement($result["response"]);
            $response = $this->__simpleXMLToArray($response);
            $return_data['response'] = $response;
            $this->waLog('===========Response of copy order============', $log_filename_dev, $log_path);
            $this->waLog($response, $log_filename_dev, $log_path);
            if(isset($response['diagnostic'])) {
                if(isset($response['diagnostic']['hits']) && $response['diagnostic']['hits'] == 1) {
                    if(isset($response['recordList']['record']['jobnumber'])) {
                        $return_data['job_number'] = $response['recordList']['record']['jobnumber'];
                    }
                }
            }
        }
        return $return_data;
    }

    function customerSearchOrCreate($data){
        $customerEmail = $data['email'];
        $customer = $this->_getCustomer($customerEmail, $data);
        if(empty($customer['priref'])) {
            $customer = $this->_createCustomer($data);
        }
        return $customer;
    }

    public function checkApi() {
        $customerEmail = 'testsever@gmail.com';
        $url = $this->api_url."?database=people&search=C8=" . $customerEmail;
        //$url = $this->api_url.$query_string;

        $this->waLog('===========API check url============', 'api_check');
        $this->waLog($url, 'api_check');

        $result = $this->__makeCurlCall(
            $url,
            "GET",
            null,
            null /* CURL GET PARAMETERS */
        );
        $return_data = [
            'code'=> $result["code"],
            'response'=> '',
        ];
        if(!empty($result["response"])) {
            $response = new \SimpleXMLElement($result["response"]);
            $response = $this->__simpleXMLToArray($response);
            $return_data['response'] = $response;
        }
        $this->waLog('===========API check response============', 'api_check');
        $this->waLog($return_data, 'api_check');
        return $return_data;
    }
}

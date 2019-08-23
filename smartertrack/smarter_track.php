<?php
class smarterTrack
{

    public function __makeCurlCall($url, $method = "GET", $headers = null, $gets = null, $posts = null)
    {
        $ch = curl_init();
        if ($gets != null) {
            $url .= "?" . (http_build_query($gets));
        }
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

    public function xmlParse(){

        $directXml = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                <soap:Body>
                    <CreateTicketResponse
                        xmlns="http://www.smartertools.com/SmarterTrack/Services2/svcTickets.asmx">
                        <CreateTicketResult>
                            <Message />
                            <RequestResult>168-24F24A57-000E</RequestResult>
                            <Result>true</Result>
                            <ResultCode>0</ResultCode>
                        </CreateTicketResult>
                    </CreateTicketResponse>
                </soap:Body>
            </soap:Envelope>';
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $directXml);
        $response = simplexml_load_string($clean_xml);
        $response = $this->__simpleXMLToArray($response);
        if(isset($response['Body']['CreateTicketResponse']['CreateTicketResult'])){
            $process_response = $response['Body']['CreateTicketResponse']['CreateTicketResult'];
            if($process_response["Result"] === true){
                $ticket_id = $process_response["RequestResult"];
                $this->waLog($ticket_id);
            }
            $this->waLog($process_response);
        }
    }

    /*
     * Create Ticket
     * https://testrgs.smartertrack.com/Services2/svcTickets.asmx?op=CreateTicket
     *
     */

    public function addTicket(){

        $url = 'https://testrgs.smartertrack.com/Services2/svcTickets.asmx';
        $directXML = '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <CreateTicket xmlns="http://www.smartertools.com/SmarterTrack/Services2/svcTickets.asmx">
                      <authUserName>admin</authUserName>
                      <authPassword>123456789</authPassword>
                      <departmentID>1</departmentID>
                      <groupId>1</groupId>
                      <userIdOfAgent>1</userIdOfAgent>
                      <toAddress>webalive.srv@gmail.com</toAddress>
                      <subject>Hello test subject</subject>
                      <body>This is test message</body>
                      <isHtml>true</isHtml>
                      <setWaiting>true</setWaiting>
                      <sendEmail>false</sendEmail>
                    </CreateTicket>
                  </soap:Body>
                </soap:Envelope>';

        $this->waLog("=========addTicket===========");
        $this->waLog($directXML);
        
        $result = $this->__makeCurlCall(
            $url,
            "POST",
            array( /* CURL HEADERS */
                "Content-Type: text/xml; charset=utf-8",
                "Accept: text/xml",
                "Pragma: no-cache",
                "Content_length: ".strlen(trim($directXML))
            ),
            '', /* CURL GET PARAMETERS */
            $directXML /* CURL POST PARAMETERS AS XML */
        );

        if($result["code"] == 200 && isset($result["response"])) {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $result["response"]);
            $response = simplexml_load_string($clean_xml);
            $response = $this->__simpleXMLToArray($response);
            if (isset($response['Body']['CreateTicketResponse']['CreateTicketResult'])) {
                $process_response = $response['Body']['CreateTicketResponse']['CreateTicketResult'];
                $this->waLog($process_response);
                if ($process_response["Result"] == 'true') {
                    $ticket_id = $process_response["RequestResult"];
                    $this->waLog($ticket_id);
                }else{

                }
            }
        }else{
            $this->waLog("Error");
            $this->waLog($result);
        }
    }

    public function addAttachment($ticket_number){

        $this->waLog("===addAttachment==");
        $url = 'https://testrgs.smartertrack.com/Services2/svcTickets.asmx';

        $path = "1.jpg";
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        //$this->waLog($data);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $base64 = base64_encode($data);

        $dateTime = date('n/d/Y H:i:s A');
        $dateTime = '2019-08-23T22:16:00';
        $directXML = '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <AddTicketAttachment xmlns="http://www.smartertools.com/SmarterTrack/Services2/svcTickets.asmx">
                      <authUserName>admin</authUserName>
                      <authPassword>123456789</authPassword>
                      <ticketNumber>'.$ticket_number.'</ticketNumber>
                      <ticketMessageId>0</ticketMessageId>
                      <fileName>1.jpg</fileName>
                      <data>'.$base64.'</data>
                      <fileLength>'.filesize($path).'</fileLength>
                      <dateCreatedUTC>'.$dateTime.'</dateCreatedUTC>
                    </AddTicketAttachment>
                  </soap:Body>
                </soap:Envelope>';

        $this->waLog($directXML);

        $result = $this->__makeCurlCall(
            $url,
            "POST",
            array( /* CURL HEADERS */
                "Content-Type: text/xml; charset=utf-8",
                "Accept: text/xml",
                "Pragma: no-cache",
                "Content_length: ".strlen(trim($directXML))
            ),
            '', /* CURL GET PARAMETERS */
            $directXML /* CURL POST PARAMETERS AS XML */
        );
        $this->waLog($result);

        if(isset($result["response"])) {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $result["response"]);
            $response = simplexml_load_string($clean_xml);
            $response = $this->__simpleXMLToArray($response);
            $this->waLog($response);
        }
    }


    function waLog ( $log, $file_name = '', $path = '' )  {

        if(!empty($path)){
            $folder = dirname(__FILE__).('/logs/'.$path);
        }else{
            $folder = dirname(__FILE__).('/logs/wa-logs');
        }

        if(!file_exists($folder)){
            mkdir($folder, 0755, true);
        }

        if (empty($file_name)) {
            $file_name = 'debug';
        }

        $file_name = $file_name . '.log';

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

$obj = new smarterTrack();
//$obj->addTicket();
$obj->addAttachment("069-24F284AF-0020");
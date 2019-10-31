<?php
include_once "configuration.php";

class Office365OutlookService
{
    public $components = ['Office365.Office365OauthService'];

    var $controller;

//    function startup(Event $event)
//    {
//        set_time_limit(0);
//        $this->controller = $this->_registry->getController();
//        $this->Session = $this->request->getSession();
//
//    }

    public function apiUrl(){
        return $api_url = Office365_api_url;
    }

    public function getUser($access_token) {
        if(empty($access_token)){
            return false;
        }
        $getUserParameters = array (
//            "\$select" => "DisplayName,EmailAddress"
        );

        $getUserUrl = $this->apiUrl()."/Me?".http_build_query($getUserParameters);

        return $this->makeApiCall($access_token, "", "GET", $getUserUrl);
    }

    public function getMessages($access_token, $user_email) {
        if(empty($access_token)){
            return false;
        }

        $getMessagesParameters = array (
            // Only return Subject, ReceivedDateTime, From and IsRead fields
            "\$select" => "Subject,ReceivedDateTime,From,IsRead",
            // Sort by ReceivedDateTime, newest first
            "\$orderby" => "ReceivedDateTime DESC",
            // Return at most 10 results
            "\$top" => "10"
        );

        //$getMessagesUrl = $this->apiUrl()."/Me/MailFolders/Inbox/Messages";
        $getMessagesUrl = $this->apiUrl()."/Me/MailFolders/Inbox/Messages?".http_build_query($getMessagesParameters);
        return $this->makeApiCall($access_token, $user_email, "GET", $getMessagesUrl);
    }

    public function getContacts($access_token, $user_email) {
        if(empty($access_token)){
            return false;
        }
        $getContactsParameters = array (
            "\$select" => "GivenName,Surname,EmailAddresses, CompanyName, MobilePhone1",
            "\$orderby" => "GivenName",
            "\$top" => "10"
        );

        $getContactsUrl = $this->apiUrl()."/Me/Contacts?".http_build_query($getContactsParameters);

        return $this->makeApiCall($access_token, $user_email, "GET", $getContactsUrl);
    }

    public function getEvents($access_token, $user_email) {
        if(empty($access_token)){
            return false;
        }
        $getEventsParameters = array (
            "\$select" => "Subject,Start,End",
            "\$orderby" => "Start/DateTime",
            "\$top" => "10"
        );

        $getEventsUrl = $this->apiUrl()."/Me/Events?".http_build_query($getEventsParameters);


        return $this->makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
    }

    public function createEvents($access_token, $user_email,$data=array()) {
        if(empty($access_token)){
            return false;
        }
        if(empty($data) || !is_array($data)){
            return false;
        }
        $data = json_encode($data);
        $getEventsUrl = $this->apiUrl()."/Me/Events";
        return $this->makeApiCall($access_token, $user_email, "POST", $getEventsUrl,$data);
    }

    public function getDrives($access_token, $user_email, $id="", $type="", $shared=null) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        if(empty($access_token)){
            return false;
        }
        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }
//        GET /me/drive/root
//        GET /me/drive/root/children
//        GET /drives/{drive-id}/items/{item-id}/children
//        GET /groups/{group-id}/drive/items/{item-id}/children
//        GET /me/drive/items/{item-id}/children
//        GET /sites/{site-id}/drive/items/{item-id}/children
//        GET /users/{user-id}/drive/items/{item-id}/children
//        GET /drives/{drive-id}/items/{item-id}/children
//        GET /drives/{drive-id}/root:/{path-relative-to-root}:/children
//        $getDrivesUrl = $this->apiUrl()."/drives/{drive-id}/root:/{path-relative-to-root}:/children";
//        $getDrivesUrl = $this->apiUrl()."/drives/bb235eef6d4f0cbf/root:/Pictures:/children";

        if($shared==false) {
            if (!empty($id)) {
                $getDrivesUrl = $this->apiUrl() . "/me/drive/items/" . $id . "/children";
            } else {
//            $getDrivesUrl = $this->apiUrl()."/me/drive/root/children";
                $getDrivesUrl = $this->apiUrl() . "/me/drive/root";
            }

            if ($type == 'file') {
                $getDrivesUrl = $this->apiUrl() . "/me/drive/items/" . $id;
            }

            return $this->makeApiCall($access_token, $user_email, "GET", $getDrivesUrl);
        }
        else{
            if (!empty($id)) {
                $getDrivesUrl = $this->apiUrl() . "/users/".Configure::read('Office365.one_drive_shared_email')."/drive/items/" . $id . "/children";
            } else {
                $getDrivesUrl = $this->apiUrl() . "/me/drive/sharedWithMe";
            }

            return $this->makeApiCall($access_token, $user_email, "GET", $getDrivesUrl);
        }


    }
    public function getTemplates($access_token, $user_email, $shared=null) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        if(empty($access_token)){
            return false;
        }
        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }

        $drive_id = $this->getDriveId($access_token, $user_email,$shared);
        if($shared===false){
            $one_drive_shared_folder = Configure::read('Office365.one_drive_shared_folder');
            if(!empty($one_drive_shared_folder)) {
                $getDrivesUrl = $this->apiUrl() . "/me/drive/root:/" . Configure::read('Office365.one_drive_shared_folder') . "/Documents:/children";
            }
            else{
                $getDrivesUrl = $this->apiUrl() . "/me/drive/root:/Documents:/children";
            }
        }
        else{
            $getDrivesUrl = $this->apiUrl()."/users/".Configure::read('Office365.one_drive_shared_email')."/drive/items/".$drive_id.":/Documents:/children";
        }

        return $this->makeApiCall($access_token, $user_email, "GET", $getDrivesUrl);
    }

    public function readPath($access_token, $user_email, $getDrivesUrl="") {
        if(empty($access_token)){
            return false;
        }
        $getDrivesUrl = $this->apiUrl().$getDrivesUrl;
        return $this->makeApiCall($access_token, $user_email, "GET", $getDrivesUrl);
    }

    public function createDrives($access_token, $user_email, $id="", $folder_name="New Folder",$return_id=false, $shared=null) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        if(empty($access_token)){
            return false;
        }
        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }

        if(empty($id)){
            $id = $this->getDriveId($access_token,$user_email, $shared);
        }
        if($shared==false){
            $parent_drive = $this->controller->Onedrive->find('first',array('conditions'=>array('Onedrive.email'=>$user_email, 'Onedrive.item_id'=>$id)));
        }
        else{
            $parent_drive = $this->controller->Onedrive->find('first',array('conditions'=>array('Onedrive.email'=>Configure::read('Office365.shared_param'), 'Onedrive.item_id'=>$id)));
        }



        if(empty($parent_drive)){
            return false;
        }

        if($shared==false){
            $drive = $this->controller->Onedrive->find('first',array('conditions'=>array('Onedrive.email'=>$user_email, 'Onedrive.name'=>$folder_name, 'Onedrive.parent_id'=>$parent_drive['Onedrive']['id'])));
        }
        else{

            $drive = $this->controller->Onedrive->find('first',array('conditions'=>array('Onedrive.email'=>Configure::read('Office365.shared_param'), 'Onedrive.name'=>$folder_name, 'Onedrive.parent_id'=>$parent_drive['Onedrive']['id'])));
        }

        if(!empty($drive)){
            $r = json_decode($drive['Onedrive']['json_data'],true);
            $r['db_table_id'] = $drive['Onedrive']['id'];


            $d = array(
                'id'=>$drive['Onedrive']['id'],
                'full_path'=>rtrim($parent_drive['Onedrive']['full_path'],'/').'/'.$folder_name,
                'onedrive_user_id'=>$this->Session->read('office365Connect.onedrive_user_id')
            );
            $this->controller->Onedrive->save($d);

            if($return_id){
                return $r['id'];
            }
            else{
                return $r;
            }
        }


        if(!empty($id)){

            if(!empty($parent_drive['Onedrive']['json_data'])) {

                if ($shared == false) {
                    $getDrivesUrl = $this->apiUrl() . "/me/drive/items/" . $id ;
                } else {
                    $getDrivesUrl = $this->apiUrl() . "/users/" . Configure::read('Office365.one_drive_shared_email') . "/drive/items/" . $id;
                }

                $getDrivesUrl = $getDrivesUrl. "/children";
                $data1 = $this->makeApiCall($access_token, $user_email, "GET", $getDrivesUrl);
                if(!empty($data1['value'])){
                    foreach ($data1['value'] as $k=>$v){
                        if(!empty($v['name']) && $v['name']==$folder_name){
                            $data = $v;
                        }
                    }

                }
            }

            if(empty($data['id'])) {
                $arr = array(
                    "name" => $folder_name,
                    "folder" => (object)array()
//            ,
//                "@microsoft.graph.conflictBehavior"=> "rename"
                );

                $json = json_encode($arr, true);

                if ($shared == false) {
                    $getDrivesUrl = $this->apiUrl() . "/me/drive/items/" . $id . "/children";
                } else {
                    $getDrivesUrl = $this->apiUrl() . "/users/" . Configure::read('Office365.one_drive_shared_email') . "/drive/items/" . $id . "/children";
                }

                $data = $this->makeApiCall($access_token, $user_email, "POST", $getDrivesUrl, $json);
            }




            if(!empty($data['id'])){
                $data1 = array(
                    'parent_id'=>$parent_drive['Onedrive']['id'],
                    'item_id'=>$data['id'],
                    'is_dir'=>1,
                    'name'=>$folder_name,
//                    'email'=>$user_email,
                    'full_path'=> rtrim($parent_drive['Onedrive']['full_path'],'/').'/'.$folder_name,
                    'is_sync'=>1,
                    'json_data'=>json_encode($data),
                    'created'=>date('Y-m-d H:i:s')
                );

                if($shared==false){
                    $data1['email'] = $user_email;
                    $data1['onedrive_user_id'] = $this->controller->Session->read('office365Connect.onedrive_user_id');
                }
                else{
                    $data1['email'] = Configure::read('Office365.shared_param');
                    $data1['onedrive_user_id'] = Configure::read('Office365.one_drive_shared_email');
                }

                $this->controller->Onedrive->create();
                if($this->controller->Onedrive->save($data1)){
                    $data['db_table_id'] = $this->controller->Onedrive->getInsertID();
                }
                else{
                    return false;
                }


                if(!empty($mime_type['MimeType']['mime_type']) && !empty($ext)){
                    $mimeType = $mime_type['MimeType']['mime_type'];
                }




                if($shared==false){
                    $getDrivesUrl = $this->apiUrl() . "/me/drive/items/" . $data['id'] . ":/empty:/content";
                }
                else{
                    $getDrivesUrl = $this->apiUrl() . "/users/".Configure::read('Office365.one_drive_shared_email')."/drive/items/" . $data['id'] . ":/empty:/content";
                }
                $mimeType = "application/octet-stream";
                $this->makeApiCall($access_token, $user_email, "ATTACHMENT", $getDrivesUrl, "",$mimeType);




                if($return_id){
                    return $data['id'];
                }
                else{
                    return $data;
                }

            }


        }

        return false;

    }


    public function recursiveDirectory($access_token, $user_email, $path="", $shared=null) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        if(empty($access_token)){
            return false;
        }
        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }

        $fillPath = "";
        $path = trim($path,"/");
        $id_ = $this->getDriveId($access_token,$user_email, $shared);
        if($shared==false){
            $drive = $this->controller->Onedrive->find('first',array('conditions'=>array('Onedrive.email'=>$user_email, 'Onedrive.name'=>'/')));
        }
        else{
            $drive = $this->controller->Onedrive->find('first',array('conditions'=>array('Onedrive.email'=>Configure::read('Office365.shared_param'), 'Onedrive.name'=>'/')));
        }

        if(empty($drive)){
            return false;
        }
        $db_parent_id = $drive['Onedrive']['id'];
        if(empty($path) || $path == "/"){

        }
        else{
            $paths = explode("/",$path);
            foreach($paths as $path_){
                if(!empty($path_)){
                    $path_ = trim($path_);
                    $fillPath = $fillPath."/".$path_;
                    $i = array();
                    $i = $this->createDrives($access_token, $user_email, $id_, $path_, false, $shared);
                    if(empty($i['id'])){
                        break;
                    }
                    $db_parent_id = $i['db_table_id'];
                    $id_ = $i['id'];

                    $d = array(
                        'id'=>$db_parent_id,
                        'full_path'=>$fillPath,
                    );
                    $this->controller->Onedrive->save($d);

                }
            }

        }
        return $id_;
    }

    public function uploadFiles($access_token, $user_email, $id, $files,$shared=null) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        if(empty($access_token)){
            return false;
        }
        if(!empty($id)){

//            $real_uploadfile = $_FILES["uploadfile"]["name"];
//            $temp = explode(".", $_FILES["uploadfile"]["name"]);
//            $temp_ral = $temp[0];
//            //if(!empty($file_name))
//            $newfilename = date("Ymd").round(microtime(true)) .$temp_ral. '.' . end($temp).$file_name;
//            $file_tmp =$_FILES['uploadfile']['tmp_name'];
//            $target_dir = "uploads/";
//            $uplode_path = $target_dir.$newfilename;
//            move_uploaded_file($file_tmp,$uplode_path);
//            //$sd = new skydrive($token);
//            $folderid = $_POST['folderid'];

            $uplode_path = Router::url('/', true) . 'css/images/cherry-logo.png';


            if($shared==false){
                $getDrivesUrl = $this->apiUrl()."/me/drive/items/" . $id . ":/cherry.jpg:/content";
            }
            else{
                $getDrivesUrl = $this->apiUrl()."/users/".Configure::read('Office365.one_drive_shared_email')."/drive/items/" . $id . ":/cherry.jpg:/content";
            }
        }

        return $this->makeApiCall($access_token, $user_email, "PUT", $getDrivesUrl, $uplode_path);
    }

    public function saveDrafts ($info){
        $t = $this->Session->read('office365Connect.access_token');
        if(empty($t)){
            return false;
        }

        $email_array= array(
            'subject' => $info['subject'],
            "body"=>array(
                "contentType"=>"HTML",
                "content"=> $info['message_body'],
            )
        );

        if(!empty($info['to'])){
            $to_email = array();
            $to= $this->comma_separated_to_array($info['to']);
            foreach ($to as $k=>$v){
                $to_email[$k]['emailAddress']['address'] = trim($v);
            }
            $email_array['toRecipients']= $to_email;
        }

        if(!empty($info['cc'])){

            if(is_array($info['cc']) && sizeof($info['cc'])>0){
                $info['cc'] = implode("; ", $info['cc']);
            }

            $cc_email = array();
            $cc= $this->comma_separated_to_array($info['cc']);
            foreach ($cc as $k=>$v){
                $cc_email[$k]['emailAddress']['address'] = $v;
            }
            $email_array['ccRecipients']= $cc_email;
        }

        if(!empty($info['email_bcc'])){
            if(is_array($info['email_bcc']) && sizeof($info['email_bcc'])>0){
                $info['email_bcc'] = implode("; ", $info['email_bcc']);
            }

            $bcc_email = array();
            $bcc= $this->comma_separated_to_array($info['email_bcc']);
            foreach ($bcc as $k=>$v){
                $bcc_email[$k]['emailAddress']['address'] = $v;
            }
            $email_array['bccRecipients']= $bcc_email;
        }

        if(!empty($info['attachment'])){
            $attachment_email = array();

            foreach ($info['attachment'] as $k=>$v){

                if(!empty($v['name'])){
                    $file_path =  $v['file'];

                    $attachment_email[$k]['@odata.type'] = '#Microsoft.Office365OutlookServices.FileAttachment';
                    $attachment_email[$k]['Name'] = $v['name'];
                    $attachment_email[$k]['ContentBytes'] = $this->base64_encode_data($file_path);
                }

            }
            $email_array['Attachments']= $attachment_email;
        }

        $json=json_encode($email_array, true);

        $getDraftUrl = $this->apiUrl()."/me/MailFolders/drafts/messages";

        return $this->makeApiCall($this->Session->read('office365Connect.access_token'), $this->Session->read('office365Connect.user_email'), "POST", $getDraftUrl, $json);

    }


    public function sendEmail ($email_id){
        $t = $this->Session->read('office365Connect.access_token');
        if(empty($t)){
            return false;
        }

        $getSendEmailUrl = $this->apiUrl()."/me/messages/" . $email_id . "/send";

        return $this->makeApiCall($this->Session->read('office365Connect.access_token'), $this->Session->read('office365Connect.user_email'), "POST", $getSendEmailUrl);

    }


//    public function sendMail ($info){
//        $t = $this->Session->read('office365Connect.access_token');
//        if(empty($t)){
//            return false;
//        }
//
//        $email_array= array(
//            "message" =>array(
//                'subject' => $info['subject'],
//                "body"=>array(
//                    "contentType"=>"HTML",
//                    "content"=> $info['message_body'],
//                )
//            ));
//
//        if(!empty($info['to'])){
//            $to_email = array();
//            $to= $this->comma_separated_to_array($info['to']);
//            foreach ($to as $k=>$v){
//                $to_email[$k]['emailAddress']['address'] = $v;
//            }
//            $email_array['message']['toRecipients']= $to_email;
//        }
//
//        if(!empty($info['cc'])){
//
//            if(is_array($info['cc']) && sizeof($info['cc'])>0){
//                $info['cc'] = implode("; ", $info['cc']);
//            }
//
//            $cc_email = array();
//            $cc= $this->comma_separated_to_array($info['cc']);
//            foreach ($cc as $k=>$v){
//                $cc_email[$k]['emailAddress']['address'] = $v;
//            }
//            $email_array['message']['ccRecipients']= $cc_email;
//        }
//
//        if(!empty($info['bcc'])){
//            if(is_array($info['bcc']) && sizeof($info['bcc'])>0){
//                $info['bcc'] = implode("; ", $info['bcc']);
//            }
//
//            $bcc_email = array();
//            $bcc= $this->comma_separated_to_array($info['bcc']);
//            foreach ($bcc as $k=>$v){
//                $bcc_email[$k]['emailAddress']['address'] = $v;
//            }
//            $email_array['message']['bccRecipients']= $bcc_email;
//        }
//
//        if(!empty($info['attachment'])){
//            $attachment_email = array();
//            foreach ($info['attachment'] as $k=>$v){
//                $file_path =  $v['file'];
//
//                $attachment_email[$k]['@odata.type'] = '#Microsoft.Office365OutlookServices.FileAttachment';
//                $attachment_email[$k]['Name'] = $v['name'];
//                $attachment_email[$k]['ContentBytes'] = $this->base64_encode_data($file_path);
//
//            }
//            $email_array['message']['Attachments']= $attachment_email;
//        }
//
//        $json=json_encode($email_array, true);
//
//        $getMessagesUrl = $this->apiUrl()."/me/sendmail";
//
//        return $this->makeApiCall($this->Session->read('office365Connect.access_token'), $this->Session->read('office365Connect.user_email'), "POST", $getMessagesUrl, $json);
//
//    }

    public function base64_encode_data($file_name = null)
    {
        if (!empty($file_name)) {
            $data = @file_get_contents($file_name);
            $base64 = chunk_split(base64_encode($data));
            return $base64;
        }
    }

    function comma_separated_to_array($string, $separator = ';')
    {
        $vals = explode($separator, $string);       //explode on comma

        foreach($vals as $key => $val) {            //trim whitespace
            $vals[$key] = trim($val);
            if(empty($vals[$key])){
                unset($vals[$key]);
            }
        }

        return array_values($vals);        //return empty array if no item found
    }

    public function makeApiCall($access_token, $user_email, $method, $url, $payload = NULL,$mimeType="", $content_length='', $content_range='') {

        //$this->Office365Logger->saveAPILog(func_get_args(), 'Request Parameter');

        //LogComponent::debug(func_get_args());

        // Generate the list of headers to always send.
        $headers = array(
            "User-Agent: php-tutorial/1.0",         // Sending a User-Agent header is a best practice.
            "Authorization: Bearer ".$access_token, // Always need our auth token!
            "Accept: application/json",             // Always accept JSON response.
            "client-request-id: ".self::makeGuid(), // Stamp each new request with a new GUID.
            "return-client-request-id: true",       // Tell the server to include our request-id GUID in the response.
            "X-AnchorMailbox: ".$user_email         // Provider user's email to optimize routing of API call
        );

        $curl = curl_init($url);

        switch(strtoupper($method)) {
            case "GET":
                $headers[] = "Content-Type: application/json";
                // Nothing to do, GET is the default and needs no
                // extra headers.
                break;
            case "POST":
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Type: application/json";
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "PATCH":
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Type: application/json";
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "PUT":
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Type: application/json";
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "ATTACHMENT____":
                // Add a Content-Type header (IMPORTANT!)
                if(empty($mimeType) ){
                    $headers[] = "Content-Type: application/octet-stream";
//                    $headers[] = "content-type: binary/octet-stream";
                }
                else{
                    $headers[] = "Content-Type: ".$mimeType;
                }

                $body = $payload;
                //$body = 'the RAW data string I want to send';

                $fp = fopen('php://temp/maxmemory:2560000', 'w');

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
//                curl_setopt($curl, CURLOPT_PUT, true);
//                curl_setopt($curl, CURLOPT_INFILE, $fp); // file pointer
//                curl_setopt($curl, CURLOPT_INFILESIZE, strlen($body));


                break;
            case "ATTACHMENT":
                // Add a Content-Type header (IMPORTANT!)
                if(empty($mimeType) || 1){
                    $headers[] = "Content-Type: application/octet-stream";
                }
                else{
                    $headers[] = "Content-Type: ".$mimeType;
                }
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "UPLOAD":
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Length: ".$content_length;
                $headers[] = "Content-Range: bytes ".$content_range;

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                exit;
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        //LogComponent::debug($response);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode >= 400) {
            $error_response =  array(
                'url'=>$url,
                'errorNumber' => $httpCode,
                'error' => 'Request returned HTTP error '.$httpCode,
                'response'=>$response
                );

            //$this->Office365Logger->saveFailureLog($error_response, 'Error Response');
        }

        $curl_errno = curl_errno($curl);
        $curl_err = curl_error($curl);

        if ($curl_errno) {
            $msg = $curl_errno.": ".$curl_err;
            curl_close($curl);
            $curl_error_response =  array('errorNumber' => $curl_errno,
                'error' => $msg);

            //$this->Office365Logger->saveFailureLog($curl_error_response, 'Error');

            return $curl_error_response;
        }
        else {
            //$this->Office365Logger->saveSuccessLog($response, 'makeApiCall');
            curl_close($curl);
            return json_decode($response, true);
        }
    }

    // This function generates a random GUID.
    public function makeGuid(){
        if (function_exists('com_create_guid')) {
            return strtolower(trim(com_create_guid(), '{}'));
        }
        else {
            $charid = strtolower(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid, 12, 4).$hyphen
                .substr($charid, 16, 4).$hyphen
                .substr($charid, 20, 12);

            return $uuid;
        }
    }

    public function uploadAttachment($access_token, $user_email, &$file=array(),$path="/",$shared=null) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        $file['size'] = filesize($file['tmp_name']);

        if(!empty($file['size']>4000000)){

            $respose = $this->getUploadSession(
                $access_token, $user_email, $file,$path,$shared
            );

            if(!empty($respose['oneDriveResponse']['uploadUrl'])){
                $upload_url = $respose['oneDriveResponse']['uploadUrl'];
//                $content_length = 8000000;
                $content_length = $file['size'];

                $initial = 0;
                $content_range_limit = $content_length;

                $loop_count = $file['size'] / $content_length;
                $n_mod = $file['size'] % $content_length;

                if($n_mod != 0){
                    $loop_count++;
                }

                for($i=1; $i<=$loop_count; $i++){

                    $content_range = $initial . "-" . ($content_range_limit-1) . '/' . $file['size'];

                    $upload_response = $this->fileUploadSession(
                        $access_token, $user_email, $upload_url, $file,$path,$shared,
                        $content_length,
                        $content_range
                    );

                    if(!empty($upload_response['oneDriveResponse']['nextExpectedRanges'][0])){
                        $initial_range = explode("-", $upload_response['oneDriveResponse']['nextExpectedRanges'][0]);

                        $count_range = $initial_range[1] - $initial_range[0];

                        if($count_range < $content_length){
                            $content_length = $file['size'] - $initial_range[0];
                            $content_range_limit = $count_range + 1;
                        }


                        $initial = $initial_range[0];
                        $content_range_limit = $content_length + $initial;

                    }else{
                        break;
                    }


                }
            }
            else{
                unset($file['oneDriveResponse']);
            }
            return $file;
        }


        if(empty($access_token)){
            return false;
        }
        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }

        $id = $this->recursiveDirectory($access_token, $user_email, $path, $shared);
        if(empty($id)){
            return false;
        }

        if(!empty($file['uploaded'])) {
            $file['tmp_name'] = str_replace("\\","/",realpath($file['tmp_name']));
//                $newfilename = date("YmdHis") . "_" . $file["name"];
            $newfilename = $file["name"];
            $uplode_path = $file['tmp_name'];


            $fileName = explode(".",$newfilename);
            $ext = array_pop($fileName);
            $mime_type = $this->controller->MimeType->find('first',array('conditions'=>array('MimeType.extension'=>strtolower($ext))));


            $mimeType = "application/octet-stream";
            if(!empty($mime_type['MimeType']['mime_type']) && !empty($ext)){
                $mimeType = $mime_type['MimeType']['mime_type'];
            }




            if($shared==false){
                $getDrivesUrl = $this->apiUrl() . "/me/drive/items/" . $id . ":/" . $newfilename . ":/content";
            }
            else{
                $getDrivesUrl = $this->apiUrl() . "/users/".Configure::read('Office365.one_drive_shared_email')."/drive/items/" . $id . ":/" . $newfilename . ":/content";
            }

            $file['oneDriveResponse'] = $this->makeApiCall($access_token, $user_email, "ATTACHMENT", $getDrivesUrl, file_get_contents($uplode_path),$mimeType);

            return $file;

        }


    }


    public function getMessageByID($access_token, $user_email, $message_id) {
        if(empty($access_token)){
            return false;
        }

        $getMessageUrl = $this->apiUrl()."/me/messages/" . $message_id;

        return $this->makeApiCall($access_token, $user_email, "GET", $getMessageUrl);
    }

    public function getAttachmentByID($access_token, $user_email, $message_id) {
        if(empty($access_token)){
            return false;
        }

        $getAttachmentUrl = $this->apiUrl()."/me/messages/" . $message_id . "/attachments";

        return $this->makeApiCall($access_token, $user_email, "GET", $getAttachmentUrl);
    }

    public function getSubscription($access_token = null, $email = null) {

        if($access_token == null){
            $access_token = $this->Session->read('office365Connect.access_token');
        }

        if($email == null){
            $email = $this->Session->read('office365Connect.access_token');
        }

        if(empty($access_token)){
            return false;
        }

        $date_time = date('Y-m-d H:i:s', strtotime('+3600 minutes'));

        $date = date('Y-m-d', strtotime($date_time));
        $time = date('H:i:s', strtotime($date_time));

        $expire_date = $date . 'T' . $time . 'Z';

        $subscription_array = array(
            "changeType" => "created,updated",
            "notificationUrl"=> Router::url('/', true, true) . "employee/contacts/notification-mail",
            "resource"=> "me/mailFolders('Inbox')/messages",
            "expirationDateTime"=> $expire_date,
            "clientState"=> "secretClientValue"
        );

        $json=json_encode($subscription_array, true);
        $subscriptionUrl = $this->apiUrl()."/subscriptions";

        return $this->makeApiCall($access_token, $email, "POST", $subscriptionUrl, $json);

    }

    public function renewSubscription($subscription_id, $access_token = null, $email = null) {
        if($access_token == null){
            $access_token = $this->Session->read('office365Connect.access_token');
        }

        if(empty($access_token)){
            return false;
        }

        $date_time = date('Y-m-d H:i:s', strtotime('+3600 minutes'));

        $date = date('Y-m-d', strtotime($date_time));
        $time = date('H:i:s', strtotime($date_time));

        $expire_date = $date . 'T' . $time . 'Z';

        $renew_subscription = array(
            "expirationDateTime"=> $expire_date
        );

        $json=json_encode($renew_subscription, true);

        $renewSubscriptionUrl = $this->apiUrl()."/subscriptions/" . $subscription_id;

        return $this->makeApiCall($access_token, $email, "PATCH", $renewSubscriptionUrl, $json);

    }


    public function targetFolderUrl($access_token,$user_email, $shared=null){
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}
        $one_drive_shared_folder = Configure::read('Office365.one_drive_shared_folder');
        if(empty($one_drive_shared_folder) || $shared==false) {
            return Configure::read('Office365.redirect_drive_url');
        }

        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }
        $id_ = $this->getDriveId($access_token,$user_email, $shared);

        if(!empty($id_)) {
            if ($shared == false) {
                $drive = $this->controller->Onedrive->find('first', array('conditions' => array('Onedrive.email' => $user_email, 'Onedrive.name' => '/')));
            } else {
                $drive = $this->controller->Onedrive->find('first', array('conditions' => array('Onedrive.email' => Configure::read('Office365.shared_param'), 'Onedrive.name' => '/')));
            }


            if(!empty($drive['Onedrive']['json_data'])){
                $json_data = json_decode($drive['Onedrive']['json_data'],true);
                $url_arr = explode("/",$json_data['webUrl']);
                $url = implode("/",array_splice($url_arr, 0, 5))."/_layouts/15/onedrive.aspx?id=";
                $url_arr = explode("/",$json_data['webUrl']);
                $url = $url.urlencode("/".implode("/",array_splice($url_arr, 3)))."&p=2";

                if(!empty($url)){
                    $this->Session->write('office365Connect.file_select_url',$url);
                    return $url;
                }

            }

        }

        return Configure::read('Office365.redirect_drive_url');
    }

    public function getUploadSession($access_token, $user_email, &$file=array(),$path="/",$shared=null) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        if(empty($access_token)){
            return false;
        }
        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }

        $id = $this->recursiveDirectory($access_token, $user_email, $path, $shared);
        if(empty($id)){
            return false;
        }

        $upload_file_array= array(
            "item"=>array(
                "@microsoft.graph.conflictBehavior"=>"rename"
            )
        );

        $json=json_encode($upload_file_array, true);

        if(!empty($file['uploaded'])) {
            $file['tmp_name'] = str_replace("\\","/",realpath($file['tmp_name']));
            $newfilename = $file["name"];


            if($shared==false){
                $getUploadSessionUrl = $this->apiUrl() . "/me/drive/items/" . $id . ":/" . $newfilename . ":/createUploadSession";
            }
            else{
                $getUploadSessionUrl = $this->apiUrl() . "/users/".Configure::read('Office365.one_drive_shared_email')."/drive/items/" . $id . ":/" . $newfilename . ":/createUploadSession";
            }

            $file['oneDriveResponse'] = $this->makeApiCall($access_token, $user_email, "POST", $getUploadSessionUrl, $json);

            return $file;

        }

    }

    public function fileUploadSession($access_token, $user_email, $upload_url, &$file=array(),$path="/",$shared=null, $content_length, $content_range) {
        if($shared==null){$shared=Configure::read('Office365.upload_to_shared');}
        if($shared!==true){$shared=false;}

        if(empty($access_token)){
            return false;
        }
        if(Configure::read('Office365.one_drive_shared_email')==$user_email){
            $shared=false;
        }

        $id = $this->recursiveDirectory($access_token, $user_email, $path, $shared);
        if(empty($id)){
            return false;
        }

        if(!empty($file['uploaded'])) {
            $file['tmp_name'] = str_replace("\\","/",realpath($file['tmp_name']));
            $newfilename = $file["name"];
            $uplode_path = $file['tmp_name'];


            $fileName = explode(".",$newfilename);
            $ext = array_pop($fileName);
            $mime_type = $this->controller->MimeType->find('first',array('conditions'=>array('MimeType.extension'=>strtolower($ext))));


            $mimeType = "application/octet-stream";
            if(!empty($mime_type['MimeType']['mime_type']) && !empty($ext)){
                $mimeType = $mime_type['MimeType']['mime_type'];
            }

            $file['oneDriveResponse'] = $this->makeApiCall($access_token, $user_email, "UPLOAD", $upload_url, file_get_contents($uplode_path),$mimeType, $content_length, $content_range);


            return $file;

        }

    }

    public function getTokenByRefreshToken($refresh_token = null){

        $client_id = Configure::read('Office365.clientId');
        $secret_id = Configure::read('Office365.clientSecret');

        $resource = "https://graph.microsoft.com";

        $token_request_data = array(
            'client_id' => $client_id,
            'client_secret' => $secret_id,
            'grant_type' => 'refresh_token',
            'resource' => $resource,
            'refresh_token' => $refresh_token
        );

        $json=http_build_query($token_request_data);

        $requestUrl = "https://login.microsoftonline.com/common/oauth2/token";

        return $this->apiCallForAccessToken("POST", $requestUrl, $json);


    }

    public function apiCallForAccessToken($method, $url, $payload = NULL) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);
        $response = json_decode($server_output, true);
        curl_close ($ch);

        return $response;
    }

    public function getAllSubscriptions($access_token) {
        if(empty($access_token)){
            return false;
        }

        $subscriptionUrl = $this->apiUrl()."/subscriptions";

        return $this->makeApiCall($access_token, "", "GET", $subscriptionUrl);
    }

    public function deleteSubscriptions($access_token, $subscription_id, $email) {
        if(empty($access_token)){
            return false;
        }

        $subscriptionUrl = $this->apiUrl()."/subscriptions(" . "'" . $subscription_id . "'" .  ")";

        return $this->makeApiCall($access_token, $email, "DELETE", $subscriptionUrl);
    }
}

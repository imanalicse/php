<?php

class Office365OauthService
{

    public $components = ['Office365.Office365OauthService', 'Office365.Office365OutlookService'];

    private static $clientId = "";
    private static $clientSecret = "";
    private static $redirect_url = "";

    private static $authority = "https://login.microsoftonline.com";
    private static $authorizeUrl = '/common/oauth2/v2.0/authorize?client_id=%1$s&redirect_uri=%2$s&response_type=code&scope=%3$s';
    private static $tokenUrl = "/common/oauth2/v2.0/token";

    private static $scopes = array(
        //"openid",
        //"offline_access",
        "Mail.Read",
        "mail.send",
//        "Mail.ReadWrite",
//        "user.read",
//        "Files.Read",
//        "Files.ReadWrite",
//        "Files.Read.All",
//        "Files.ReadWrite.All",
//        "Sites.ReadWrite.All",
//        "Calendars.Read",
//        "Calendars.Read.Shared",
//        "Calendars.ReadWrite",
//        "Calendars.ReadWrite.Shared",
//        "contacts.read"
    );

    function __construct()
    {
        self::$clientId = Office365_clientId;
        self::$clientSecret = Office365_clientSecret;
        self::$redirect_url = Office365_redirect_url;
    }


    public static function getLoginUrl($redirectUri) {

        $scopestr = implode(" ", self::$scopes);
        $loginUrl = self::$authority.sprintf(self::$authorizeUrl, self::$clientId, urlencode($redirectUri), urlencode($scopestr));

        return $loginUrl;
    }

    public static function getTokenFromAuthCode($authCode, $redirectUri) {
        return self::getToken("authorization_code", $authCode, $redirectUri);
    }

    public static function getTokenFromRefreshToken($refreshToken, $redirectUri) {
        return self::getToken("refresh_token", $refreshToken, $redirectUri);
    }

    public static function getToken($grantType, $code, $redirectUri) {
        $parameter_name = $grantType;
        if (strcmp($parameter_name, 'authorization_code') == 0) {
            $parameter_name = 'code';
        }

        // Build the form data to post to the OAuth2 token endpoint
        $token_request_data = array(
            "grant_type" => $grantType,
            $parameter_name => $code,
            "redirect_uri" => $redirectUri,
            "scope" => implode(" ", self::$scopes),
            "client_id" => self::$clientId,
            "client_secret" => self::$clientSecret
        );

        // Calling http_build_query is important to get the data
        // formatted as expected.
        $token_request_body = http_build_query($token_request_data);

        $curl = curl_init(self::$authority.self::$tokenUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            return array('errorNumber' => $httpCode,
                'error' => 'Token request returned HTTP error '.$httpCode);
        }

        // Check error
        $curl_errno = curl_errno($curl);
        $curl_err = curl_error($curl);
        if ($curl_errno) {
            $msg = $curl_errno.": ".$curl_err;
            return array('errorNumber' => $curl_errno,
                'error' => $msg);
        }

        curl_close($curl);

        $json_vals = json_decode($response, true);

        return $json_vals;
    }

    public function getAccessToken() {
        $redirectUri = self::$redirect_url;

        //$current_token = $this->Session->read('office365Connect.access_token');

        $this->EmailSettings = TableRegistry::getTableLocator()->get('Office365.EmailSettings');
        $email_setting_data = $this->EmailSettings->find()->first();
        $current_token = $email_setting_data['access_token'];

        if (!empty($current_token)) {
//            $expiration = '';
//            if($this->Session->check('office365Connect.token_expires')){                              // Check expiration
//                $expiration = $this->Session->read('office365Connect.token_expires');
//            }

            $expiration = $email_setting_data['token_expires'];

            if ($expiration < time()) {
                // Token expired, refresh
                $refresh_token = $this->Session->read('office365Connect.refresh_token');
                $refresh_token = $email_setting_data['refresh_token'];

                $new_tokens = self::getTokenFromRefreshToken($refresh_token, $redirectUri);

                if(empty($new_tokens['access_token'])){
                    $this->Session->delete('office365Connect.access_token');
                    $this->Session->delete('office365Connect.refresh_token');
                    $this->Session->delete('office365Connect.redirect_url');
                    $this->Session->delete('office365Connect.user_email');
                    return null;
                }

                // Update the stored tokens and expiration
                $this->Session->write('office365Connect.access_token', $new_tokens['access_token']);
                $this->Session->write('office365Connect.refresh_token', $new_tokens['refresh_token']);


                $expiration = time() + $new_tokens['expires_in'];
                $this->Session->write('office365Connect.token_expires', $expiration);

                $user = $this->Office365OutlookService->getUser($new_tokens['access_token']);



                $this->EmailSettings = TableRegistry::getTableLocator()->get('Office365.EmailSettings');

                $is_exist = $this->EmailSettings->find()->first();

                $email_setting = array();

                $email_setting['onedrive_user_id'] = $user['id'];
                $email_setting['given_name'] = $user['givenName'];
                $email_setting['surname'] = $user['surname'];
                $email_setting['display_name'] = $user['displayName'];

                $email_setting['email'] = $user['userPrincipalName'];
                $email_setting['access_token'] = $new_tokens['access_token'];
                $email_setting['refresh_token'] = $new_tokens['refresh_token'];
                $email_setting['host'] = $_SERVER['HTTP_HOST'];
                $email_setting['token_expires'] = $expiration;

                if(empty($is_exist)){
                    $emailSetting = $this->EmailSettings->newEntity();
                    $email_setting['user_id'] = $this->Session->read('Auth.User.id');
                    $email_setting['redirect_url'] = self::$redirect_url;
                }else{
                    $emailSetting = $this->EmailSettings->get($is_exist->id);
                }

                $emailSetting = $this->EmailSettings->patchEntity($emailSetting, $email_setting);

                $this->EmailSettings->save($emailSetting);



                return $new_tokens['access_token'];     // Return new token
            }else {
                return $current_token;            // Token is still valid, return it
            }
        }
        else {
            return null;
        }
    }

    public function configureOffice365()
    {

        $this->EmailSettings = TableRegistry::getTableLocator()->get('Office365.EmailSettings');


        $email_setting = $this->EmailSettings->find()->enableHydration(false)->first();

//        if(!empty($email_setting)){
//            if(time()>$email_setting['access_expires']){
//                //$this->EmailSettings->delete($email_setting['EmailSettings']['id']);
//            }else{
//                $access_expire_time = time() + 24*60*60;
//                //$data = array('id' => $email_setting->id, 'access_expires' => $access_expire_time);
//                $emailSetting = $this->EmailSettings->get($email_setting['id']);
//                $emailSetting->access_expires = $access_expire_time;
//                $this->EmailSettings->save($emailSetting);
//            }
//        }

        $redirectUri = Configure::read('Office365.redirect_url');
        $loginUrl = $this->getLoginUrl($redirectUri);
        $this->controller->set('oneDriveLoginUrl',$loginUrl);


        if (empty($email_setting['access_token'])
            || !$this->Session->check('office365Connect.access_token')
            || $email_setting['access_token'] != !$this->Session->read('office365Connect.access_token')) {
            $this->Session->delete('office365Connect.access_token');
            $this->Session->delete('office365Connect.refresh_token');
            $this->Session->delete('office365Connect.redirect_url');
            $this->Session->delete('office365Connect.user_email');
            $this->Session->delete('office365Connect.display_name');
            $this->Session->delete('office365Connect.is_account');
            $this->Session->delete('office365Connect.onedrive_user_id');
        }



        if (!empty($email_setting['access_token'])){
            $this->Session->delete('office365Connect.driveId');
            $this->Session->write('office365Connect.access_token', $email_setting['access_token']);
            $this->Session->write('office365Connect.refresh_token', $email_setting['refresh_token']);
            $this->Session->write('office365Connect.redirect_url', $email_setting['redirect_url']);
            $this->Session->write('office365Connect.user_email', $email_setting['email']);
            $this->Session->write('office365Connect.display_name', $email_setting['display_name']);
            $this->Session->write('office365Connect.onedrive_user_id', $email_setting['onedrive_user_id']);
            $this->Session->write('office365Connect.token_expires', $email_setting['token_expires']);
            $this->Session->delete('office365Connect.disable');
        }
    }

    public function getUserAccessToken($mail_id){
        $access_token = '';

        if(!empty($mail_id)){

            $this->EmailSettings = TableRegistry::getTableLocator()->get('Office365.EmailSettings');

            $settings_details = $this->EmailSettings->find()->where(['email' =>$mail_id])->first();
            if(!empty($settings_details->access_token)){
                $access_token = $settings_details->access_token;
            }
        }
        return $access_token;
    }

    public function update_subscriptions(){

        $current_date = date('Y-m-d');

        $this->EmailSubscriptions = TableRegistry::getTableLocator()->get('Office365.EmailSubscriptions');

        $current_initial_date_time = $current_date . ' 00:00:00';

        $subscriptions = $this->EmailSubscriptions->find()->where(['updated <' => $current_initial_date_time])->select(['id', 'subscription_id', 'subscription_expiration_date_time', 'email', 'updated']);

        if(!empty($subscriptions)){
            foreach ($subscriptions as $v){
                if($this->Session->read('office365Connect.user_email') == $v->email && $this->Session->check('office365Connect.access_token')){
                    $access_token = $this->Session->read('office365Connect.access_token');
                }else{
                    $access_token = $this->getNewTokenByEmail($v->email);
                }

                $subscription_response = '';

                if(!empty($access_token)){
                    if(!empty($v->subscription_expiration_date_time)){
                        $subscription_time = explode('T', $v->subscription_expiration_date_time);

                        $subscription_date = $subscription_time[0];

                        if(strtotime($current_date) >= strtotime($subscription_date)) {
                            $subscription_response = $this->Office365OutlookService->getSubscription($access_token, $v->email);                       //create subscription
                        }else{
                            $subscription_response = $this->Office365OutlookService->renewSubscription($v->subscription_id, $access_token);               // Renew subscription
                        }
                    }

                    if(!empty($subscription_response['id'])) {

                        $emailSubscription = $this->EmailSubscriptions->get($v->id);

                        $emailSubscription->subscription_id = $subscription_response['id'];
                        $emailSubscription->resource = $subscription_response['resource'];
                        $emailSubscription->change_type = $subscription_response['changeType'];
                        $emailSubscription->notification_url = $subscription_response['notificationUrl'];
                        $emailSubscription->subscription_expiration_date_time = $subscription_response['expirationDateTime'];
                        $emailSubscription->response_data = json_encode($subscription_response);
                        $emailSubscription->updated = date('Y-m-d H:i:s');

                        if($this->EmailSubscriptions->save($emailSubscription)){
                            //Log::info('$emailSubscription: true');
                        }else{
                            //Log::info('$emailSubscription: false');
                        }
                    }
                }
            }
        }
    }

    public function getNewTokenByEmail($email){

        $this->EmailSettings = TableRegistry::getTableLocator()->get('Office365.EmailSettings');
        $settings_details = $this->EmailSettings->find()->where(['email' =>$email])->first();

        if(!empty($settings_details->refresh_token)){
            $access_token = $settings_details->access_token;
            $expiration = $settings_details->token_expires;
            if ($expiration < time()) {
                $refresh_token = $settings_details->refresh_token;

                $new_tokens = $this->Office365OutlookService->getTokenByRefreshToken($refresh_token);

                //$new_tokens = self::getTokenFromRefreshToken($refresh_token, $redirectUri);

                $email_setting = array();

                $expiration = time() + $new_tokens['expires_in'];

                $emailSetting = $this->EmailSettings->get($settings_details->id);

                $emailSetting->access_token = $new_tokens['access_token'];
                $emailSetting->refresh_token = $new_tokens['refresh_token'];
                $emailSetting->token_expires = $expiration;

                $this->EmailSettings->save($emailSetting);

                return $new_tokens['access_token'];
            }else{
                return $access_token;
            }

        }
    }

    public function getTokenByRefreshToken($refresh_token){
        $token = '';
        $new_tokens = $this->Office365OutlookService->getTokenByRefreshToken($refresh_token);
        if(!empty($new_tokens)){
            $token = $new_tokens['access_token'];
        }

        return $token;
    }

    public function getTokenInfo($refresh_token = null){
        //$refresh_token = 'OAQABAAAAAADXzZ3ifr-GRbDT45zNSEFEwVIPUOk4b8J3heRn-mGdJQG3CpDPEmGDfI-Ruu4t38RIoyHfc_5xviYtZDn1YbwMMNFN06UAYCtfr9Eg8PcaIzeO6OxJ-qWDuY42e_ChxDazjSepBb8hbpDqskvioSAGiZpgpnZSuSPblY3AsSTTCP6EFH_2N8e4AJuJTeaQpmlF4ygBA7AM1sc7KSS2WE3xz-ztHOTp9zW7Q-ZiZxVhk_LrOlIOzfwKDR3BJ_ZpAN-QNg1RsfSqCLxP_YbGjGeVUY1Ki0UfGSzajx6qg-d1Y-_rIDUPYwq_BXa7_ff0fVtUGsb5xwqPC5PJJe684Le1UkPXZ7JAjnvE3zLMrACxE3CGusDctRbXLOFt7P4B-u67r7mA7lfXR8flfBktXWfeXS3QK8nCnLf_xyBf9Efk37GJwp3F7e7pZYrrekNXDND_ZalyYCqZ-IiwVYD0sVYsHM89sq5u6F3uoXCiWobi_Ddv8CPr8XvEcd4Zd-vXCm_UeSnbpfY4X0l0QGyaZ_XtSXQaJVbAPWlU_bn36nezQitVIAFGFwEbuJrzBX_6iz9RNNyx81Btd7YR4PrTpAL0rtLTtzYuY3UyAcMjzu3k9lCdZkKurBJGftx0_HzFb2t6j73ntJNMIjhoNGh8EKR4QciKnXjEETqA4BEubpNRSyW6MmF-exC0lk_WwdU9PXq7PRSxye2VKIc4G15EwyYxfzyfSeyHyE61G2_pv8JpPaS1SkG3Y1n5cMcVF0YU79bFwOifYr5qr1ABvgmgCkCYbaRvWpklDsqKIKq1gWc4pxX1QoMTERE91EghL_6gddogAA';
        $redirectUri = self::$redirect_url;
        //$redirectUri = 'https://pip.echogroup.co/office365/contacts/authorize';
        $new_tokens = self::getTokenFromRefreshToken($refresh_token, $redirectUri);

        return $new_tokens;
    }

}

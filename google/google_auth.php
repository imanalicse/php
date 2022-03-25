<?php
require "GoogleAnalytics.php";
require '../vendor/autoload.php';
require './../functions.php';
require './../DotEnv.php';
(new DotEnv(__DIR__ . '/.env'))->load();

use GuzzleHttp\Client;

waLog('$_REQUEST', 'google_auth');
waLog($_REQUEST, 'google_auth');

if (isset($_GET['code']) && !empty($_GET['code'])) {

    $authOptions = GoogleAnalytics::getoAuthOptions();
    $url = $authOptions['tokenUrl'];

    $requestBody = array(
        'code' => $_GET['code'],
        'client_id' => getenv('google_analytics_client_id'),
        'client_secret' => getenv('google_analytics_client_secret'),
        'redirect_uri' => $authOptions['redirectUrl'],
        'grant_type' => 'authorization_code'
    );
    waLog('$requestBody', 'google_auth');
    waLog($requestBody, 'google_auth');

    $http = new Client(['Content-Type' => 'application/x-www-form-urlencoded']);
    //$headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
    $response = $http->post($url, $requestBody);
    waLog('$response', 'google_auth');
    waLog($response, 'google_auth');

    if (!empty($response->getStatusCode()) && $response->getStatusCode() == 200) {
        $responseBody = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($response->getStringBody()));
        $result = json_decode($responseBody, true);

        $googleOauth = $this->getTableLocator()->get('GoogleOauth');


        if (!empty($result['refresh_token'])) {

            //$refreshToken = $googleOauth->find()->where(['refresh_token' => $result['refresh_token']])->first();

            $refreshToken = $googleOauth->find()->first();

            if (!empty($refreshToken)) {
                $deleted = $this->getDbTable('GoogleOauth')->delete($refreshToken);
            }

            $entity = $googleOauth->newEntity();
            $oAuth_info = $googleOauth->patchEntity($entity, $result);
            $oAuth_info['created'] = date("Y-m-d H:i:s");
            $oAuth_info['updated'] = date("Y-m-d H:i:s");

            if ($googleOauth->save($oAuth_info)) {
                $this->Flash->adminSuccess("Google analytics connection Successfull!", ['key' => 'admin_success']);
                $this->redirect(array('action' => 'index'));
            }

        }
        else {
            $googleOauthInfo = $googleOauth->find()->first();

            if (empty($googleOauthInfo)) {

                $entity = $googleOauth->newEntity();
                $oAuth_info = $googleOauth->patchEntity($entity, $result);
                $oAuth_info['created'] = date("Y-m-d H:i:s");
                $oAuth_info['updated'] = date("Y-m-d H:i:s");

                if ($googleOauth->save($oAuth_info)) {
                    $this->Flash->adminSuccess("Google analytics connection Successfull!", ['key' => 'admin_success']);
                    $this->redirect(array('action' => 'index'));
                }
            }
            else {
                $googleOauthInfo['access_token'] = $result['access_token'];
                $googleOauthInfo['scope'] = $result['scope'];
                $googleOauthInfo['token_type'] = $result['token_type'];
                $googleOauthInfo['created'] = date("Y-m-d H:i:s");
                $googleOauthInfo['updated'] = date("Y-m-d H:i:s");
                if ($googleOauth->save($googleOauthInfo)) {
                    $this->Flash->adminSuccess("Google analytics connection Successfull!", ['key' => 'admin_success']);
                    $this->redirect(array('action' => 'index'));
                }
            }

        }
    }


}
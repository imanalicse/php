<?php
include_once '../configuration.php';
include_once '../Office365OauthService.php';
include_once "../Office365OutlookService.php";

$auth_code = $_REQUEST['code'];

$Office365OauthService = new Office365OauthService();
$Office365OutlookService = new Office365OutlookService();
$tokens = $Office365OauthService->getTokenFromAuthCode($auth_code, Office365_redirect_url);

echo '<pre>';
print_r($tokens);
echo '</pre>';
if (!empty($tokens['access_token'])) {
    $user = $Office365OutlookService->getUser($tokens['access_token']);

    echo '<pre>';
    print_r($user);
    echo '</pre>';
}
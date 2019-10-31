<?php
include_once 'configuration.php';
include_once 'Office365OauthService.php';

$Office365OauthService = new Office365OauthService();

$loginUrl = $Office365OauthService->getLoginUrl($redirectUri);
echo '<pre>';
print_r($loginUrl);
echo '</pre>';

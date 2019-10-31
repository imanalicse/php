<?php
include_once 'configuration.php';
include_once 'Office365OauthService.php';

$Office365OauthService = new Office365OauthService();

$redirectUri = Office365_redirect_url;

$loginUrl = $Office365OauthService->getLoginUrl($redirectUri);
?>
    <div align="center" style="font-size:20px; ">Please <strong ><a href="<?php echo $loginUrl; ?>">sign in</a> </strong>with your Office 365 or Outlook.com account.</div>
<?php

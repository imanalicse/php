<?php 
session_start();
require_once 'settings.php';
require_once 'config.php';

$login_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me') . '&redirect_uri=' . urlencode($redirect_url) . '&response_type=code&client_id=' . $client_id . '&access_type=online';


if( isset($_GET['code']) ) {
    try {
        $gapi = new GoogleLoginApi();

        //Get Access Token Data
        $data = $gapi->GetAccessToken($client_id, $redirect_url, $client_secret, $_GET['code']);

        // Get User Information
        $user_info = $gapi->GetUserProfileInfo($data['access_token']);

        echo '<pre>'; var_dump($user_info); echo '</pre>';
    }
    catch(Exception $e) {
        echo $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Google Login</title>
</head>
<body>
    <?php if( !isset($_GET['code']) ) : ?>
        <a href="<?php echo $login_url ?>">Login with Google</a>
    <?php endif; ?>
</body>
</html>
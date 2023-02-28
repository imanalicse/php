<?php
session_start();
function getAccessTokenFromStorage() : string {
    $access_token = '';
    if (isset($_SESSION['token_data'])) {
        if (!isset($_SESSION['token_data']['expires_in']) || ($_SESSION['token_data']['expires_in'] - 60) <= time()) {
            unset($_SESSION['token_data']);
        }
        elseif (isset($_SESSION['token_data']['access_token']) && !empty($_SESSION['token_data']['access_token'])) {
            $access_token = $_SESSION['token_data']['access_token'];
        }
    }
    return $access_token;
}
$access_token = getAccessTokenFromStorage();
echo "<pre>";
print_r($access_token);
echo "</pre>";
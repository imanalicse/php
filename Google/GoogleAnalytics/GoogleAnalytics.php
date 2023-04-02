<?php

namespace App\Google\GoogleAnalytics;
use FunctionsUtils;

require "../../Utils/FunctionsUtils.php";
require './../../DotEnv.php';
(new DotEnv(__DIR__ . '/.env'))->load();

class GoogleAnalytics
{
    public function connectToGoogle()
    {
        $authOptions = self::getoAuthOptions();
        $auth_url = $authOptions['authUrl'];
        $auth_url .= "?response_type=code";
        $auth_url .= "&redirect_uri=" . urlencode($authOptions['redirectUrl']);
        $auth_url .= "&client_id=" . getenv('google_analytics_client_id');
        $auth_url .= "&access_type=offline";
        $auth_url .= "&include_granted_scopes=true";
        $auth_url .= "&scope=" . urlencode($authOptions['analyticsUrl']) . ' ' . urlencode($authOptions['analyticsUrl2']);

        FunctionsUtils::redirect($auth_url);
    }

    public static function getoAuthOptions()
    {
        return [
            'authUrl' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'tokenUrl' => 'https://accounts.google.com/o/oauth2/token',
            'redirectUrl' => 'http://localhost:8000/google_auth.php',
            'analyticsUrl' => 'https://www.googleapis.com/auth/analytics',
            'analyticsUrl2' => 'https://www.googleapis.com/auth/analytics.readonly'
        ];
    }
}
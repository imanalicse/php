<?php

function getAccessTokenFromStorage() : string {
   $access_token = '';
   if (isset($_COOKIE['x_access_token']) && !empty($_COOKIE['x_access_token'])) {      
        $token_data = json_decode($_COOKIE['x_access_token'], true);    
         $access_token = $token_data['x_access_token'] ?? '';       
   }
   return $access_token;
}
$access_token = getAccessTokenFromStorage();
echo "<pre>";
print_r($access_token);
echo "</pre>";
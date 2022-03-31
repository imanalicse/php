<?php
function getLocationName() {

	//$ip = '141.168.138.15'; // Victoria/Melbourne
	//$ip = '49.197.229.133'; // Queensland/Brisbane
	//$ip = '114.141.196.226'; // New South Wales/Sydney
	//$ip = '58.108.79.123'; // Western Australia/Perth
	$ip = $_SERVER['REMOTE_ADDR'];

	if(isset($_COOKIE['locationName'])) {
        return $_COOKIE['locationName'];
    }

	$locationName = 'Outside';

	try{
        $location_data = @file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip);
        $locData = unserialize($location_data);
    }catch(Exception $ex){
        $locData = array();
    }

    if(!empty($locData) && $locData['geoplugin_countryCode'] == 'AU'){
        $australia_state_city = array(
            'Victoria' => 'Melbourne',
            'New South Wales' => 'Sydney',
            'Queensland' => 'Brisbane',
            'Western Australia' => 'Perth',
            'South Australia' => 'Adelaide',
            'Australian Capital Territory' => 'Canberra',
            'Tasmania' => 'Hobart',
            'Northern Territory' => 'Darwin'
        );
        $state = $locData['geoplugin_regionName'];

        if(!empty($australia_state_city[$state])) {
            $locationName = $australia_state_city[$state];
        }
    }

    setcookie( 'locationName', $locationName, time() + (60*60*24*7), COOKIEPATH, COOKIE_DOMAIN   );

	return $locationName;
}
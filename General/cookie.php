<?php
//function cookieArray($var, $limit=4096, $cookie_name="my_cookie")
//{
//    //check if we have cookie
//    if(isset($_COOKIE[$cookie_name]))
//    {
//
//        //decode cookie string from JSON
//        $cookieArr = (array) json_decode($_COOKIE[$cookie_name]);
//
//        //push additional data
//        array_push($cookieArr, $var);
//
//        //remove empty values
//        foreach($cookieArr as $k => $v){ if($v == '' || is_null($v) || $v == 0){ unset($cookieArr[$k]); } }
//
//        //encode data again for cookie
//        $cookie_data = json_encode($cookieArr);
//
//        //need to limit cookie size. Default is set to 4Kb. If it is greater than limit. it then gets reset.
//        $cookie_data = mb_strlen($cookie_data) >= $limit ? '' : $cookie_data;
//
//        //destroy cookie
//        setcookie($cookie_name, '', time()-3600 , '/');
//
//        //set cookie with new data and expires after a week
//        setcookie($cookie_name, $cookie_data, time()+(3600*24*7) , '/');
//
//
//    }else{
//
//        //create cookie with json string etc.
//        $cookie_data = json_encode($var);
//        //set cookie expires after a week
//        setcookie($cookie_name, $cookie_data, time()+(3600*24*7) , '/');
//
//    }//end if
//
//}
$cookie_name = "test_cookie";

$cookie_data = array(
    'hello'=>'hello world',
    'abc'=>'abccccc'
);
$cookie_data = json_encode($cookie_data);
//set cookie expires after a week
setcookie($cookie_name, $cookie_data, time()+(3600*24*7) , '/');
if(isset($_COOKIE['test_cookie'])){

    echo "<pre>";
    print_r(json_decode($_COOKIE['test_cookie'], true));
    echo "</pre>";
}

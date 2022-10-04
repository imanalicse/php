<?php
$date = '31/12/2022';
$date = str_replace("/","-",$date);
$new_data = date('jS F Y', strtotime($date));
echo "<pre>";
print_r($new_data);
echo "</pre>";
<?php
$date = "04-15-2013";
$date1 = str_replace('-', '/', $date);
echo '<pre>';
print_r($date1);
echo '</pre>';
$tomorrow = date('m-d-Y',strtotime($date1 . "+1 days"));
echo '<pre>';
print_r($tomorrow);
echo '</pre>';
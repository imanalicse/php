<?php
/*
 *
 * */
$text = "He was eating cake in the cafe.";
$count= preg_match_all('/ca[kf]e/', $text, $matches);
echo '<pre>';
print_r($count);
echo '</pre>';
echo '<pre>';
print_r($matches);
echo '</pre>';
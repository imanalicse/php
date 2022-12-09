<?php
/**
 * pathinfo — Returns information about a file path
 */

 $path_info = pathinfo(__FILE__);
 echo "<pre>";
 print_r($path_info);
 echo "</pre>";

 /*
 Array
(
    [dirname] => D:\wamp\www\codehub\php\BuildInFunction\Filesystem
    [basename] => pathinfo.php
    [extension] => php
    [filename] => pathinfo
)
  */

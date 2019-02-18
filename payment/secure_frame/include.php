<?php

function get_base_url(){
    $base_url = 'http://localhost/codehub/php/payment';
    return $base_url;
}

if (!function_exists('wa_log')) {
    function wa_log ( $log, $file_name = '' )  {
        if (empty($file_name)) {
            $file_name = 'debug';
        }

        $file_name = $file_name . '.log';
        $folder = dirname(__FILE__).'/wa-logs';

        if(!file_exists($folder)){
            mkdir($folder, 0755);
        }

        $file_path = $folder.'/' . $file_name;

        if (is_array($log) || is_object($log)) {
            $log_data = print_r($log, true);
        } else {
            $log_data = $log;
        }

        $log_data = date('Y-m-d H:i:s') . " Debug: \n" . $log_data."\n\n";

        error_log($log_data, 3, $file_path);
    }
}
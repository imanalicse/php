<?php
namespace App\Logger;

class Log
{
   public static function write( $log, $file_name = '', $path = '' )  {
        if(!empty($path)){
            $folder = dirname(__FILE__).('/logs/'.$path);
        }else{
            $folder = dirname(__FILE__).('/logs/wa-logs');
        }

        if(!file_exists($folder)){
            mkdir($folder, 0755, true);
        }

        if (empty($file_name)) {
            $file_name = 'debug';
        }

        $file_name = $file_name . '.log';

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
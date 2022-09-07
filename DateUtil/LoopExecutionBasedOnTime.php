<?php
require '../vendor/autoload.php';

use App\Logger\Log;
$start_time = time();
Log::write('$start_time: '. $start_time);
for ($i = 0; $i < 10000000000; $i++) {
    $current_time = time();
    $time_different = $current_time - $start_time;
    if ($time_different > 10) {
        break;
    }
    Log::write('$current_time: '.$current_time . ' $time_different: '. $time_different);
    sleep(1);
}
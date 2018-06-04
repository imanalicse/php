<?php
echo '<pre>';
print_r($_REQUEST);
echo '</pre>';

$logFile = __DIR__ . '/ipn.log';
$logFile = fopen($logFile, "a");
$logData = date('Y-m-d h:i:s') . ':  ' . json_encode($_REQUEST) . "\n\n\n";
fwrite($logFile, $logData);
fclose($logFile);

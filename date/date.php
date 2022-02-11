<?php

$start_date = '2021-08-01';
$end_date = '2022-02-05';
$start_date = date_create($start_date);
$end_date   = date_create($end_date); // If you want to include this date, add 1 day

$interval = DateInterval::createFromDateString('1 month');
$date_range = new DatePeriod($start_date, $interval ,$end_date);
$date_periods = [];

foreach ($date_range as $date) {
    $date_periods[] = $date->format('Y-m-d');
}
echo "<pre>";
print_r($date_periods);
echo "</pre>";
$total_period = count($date_periods);
$date_slot = [];
for ($i = 0; $i < $total_period; $i++) {
    if ($i == $total_period -1) continue;
    $date_slot[$i]["start_date"] = $date_periods[$i];
    //$date_slot[$i]["end_date"] = $date_periods[$i+1];
    $date_slot[$i]["end_date"] = date('Y-m-t', strtotime($date_periods[$i]));
}

echo "<pre>";
print_r($date_slot);
echo "</pre>";
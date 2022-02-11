<?php

$start_date = '2021-08-10';
$end_date = '2022-02-05';

$initial_date_interval = [];
if ($start_date != date('Y-m-01', strtotime($start_date))) {
    $initial_date_interval = [
        'start_date' => $start_date,
        'end_date' => date('Y-m-t', strtotime($start_date)),
    ];
    $start_date = date('Y-m-01', strtotime($start_date . ' +1 month'));
}
$last_date_interval = [];
if ($end_date != date('Y-m-t', strtotime($end_date))) {
    $last_date_interval = [
        'start_date' => date('Y-m-01', strtotime($end_date)),
        'end_date' => $end_date,
    ];
}

$start_date = date_create($start_date);
$end_date   = date_create($end_date);

$interval = DateInterval::createFromDateString('1 month');
$date_range = new DatePeriod($start_date, $interval ,$end_date);
$date_periods = [];

foreach ($date_range as $date) {
    $date_periods[] = $date->format('Y-m-d');
}

$total_period = count($date_periods);
$date_slot = [];
for ($i = 0; $i < $total_period; $i++) {
    if ($i == $total_period -1) continue;
    $date_slot[$i]["start_date"] = $date_periods[$i];
    //$date_slot[$i]["end_date"] = $date_periods[$i+1];
    $date_slot[$i]["end_date"] = date('Y-m-t', strtotime($date_periods[$i]));
}

if (!empty($initial_date_interval)) {
    array_unshift($date_slot, $initial_date_interval);
}
if (!empty($last_date_interval)) {
    $date_slot[] = $last_date_interval;
}

echo "<pre>";
print_r($date_slot);
echo "</pre>";
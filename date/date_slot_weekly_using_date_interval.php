<?php

$start_date = '2021-12-01';
$end_date = '2022-02-05';

$week_start_day_name = 'Monday';
$week_end_day_name = 'Sunday';
$week_start_date_of_start_date =  date("Y-m-d", strtotime("$start_date $week_start_day_name this week"));
$week_end_date_of_start_date =  date("Y-m-d", strtotime("$start_date $week_end_day_name this week"));
$initial_date_interval = [];
if ($start_date !== $week_start_date_of_start_date) {
    $initial_date_interval = [
        'start_date' => $start_date,
        'end_date' => $week_end_date_of_start_date,
    ];
    $start_date = date("Y-m-d", strtotime("$week_end_date_of_start_date +1 day"));
}

$week_start_date_of_end_date =  date("Y-m-d", strtotime("$end_date $week_start_day_name this week"));
$week_end_date_of_end_date =  date("Y-m-d", strtotime("$end_date $week_end_day_name this week"));
$last_date_interval = [];
if ($end_date !== $week_end_date_of_end_date) {
    $last_date_interval = [
        'start_date' => $week_start_date_of_end_date,
        'end_date' => $end_date,
    ];
}

$start_date_obj = date_create($start_date);
$end_date_obj   = date_create($end_date);

$interval = DateInterval::createFromDateString('1 week');
$date_range = new DatePeriod($start_date_obj, $interval ,$end_date_obj);
$date_periods = [];
foreach ($date_range as $date) {
    $date_periods[] = $date->format('Y-m-d');
}

$total_period = count($date_periods);
$date_slot = [];
for ($i = 0; $i < $total_period; $i++) {
    if ($i == $total_period -1) continue;
    $date_slot[$i]["start_date"] = $date_periods[$i];
    $date_slot[$i]["end_date"] = date('Y-m-d', strtotime($date_periods[$i+1] . '-1 day'));
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
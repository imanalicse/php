<?php
function getTimeSlot($interval, $start_time, $end_time)
{
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $startTime = $start->format('H:i');
    $endTime = $end->format('H:i');
    $i=0;
    $time = [];
    while(strtotime($startTime) <= strtotime($endTime)){
        $start = $startTime;
        $end = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
        $startTime = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
        $i++;
        if(strtotime($startTime) <= strtotime($endTime)){
            $time[$i]['slot_start_time'] = $start;
            $time[$i]['slot_end_time'] = $end;
        }
    }
    return $time;
}

$slots = getTimeSlot(30, '10:00', '13:00');

function getDateSlotInterval($interval, $start_time, $end_time)
{
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $startTime = $start->format('Y-m-d');
    $endTime = $end->format('Y-m-d');
    $i = 0;
    $time = [];
    while(strtotime($startTime) <= strtotime($endTime)){
        $start = $startTime;
        $end = date('Y-m-d',strtotime('+'.$interval, strtotime($startTime)));
        $startTime = date('Y-m-d',strtotime('+'.$interval, strtotime($startTime)));
        $i++;
        if(strtotime($startTime) <= strtotime($endTime)){
            $time[$i]['start_date'] = $start;
            //$time[$i]['end_date'] = $end;
            $time[$i]['end_date'] = date("y-m-t", strtotime($start));
        }
    }
    return $time;
}

$start_date = '2021-08-10';
$end_date = '2022-02-05';
$initial_date_interval = [];
if ($start_date != date('Y-m-01',strtotime($start_date))) {
    $initial_date_interval = [
        'start_date' => $start_date,
        'end_date' => date('Y-m-t',strtotime($start_date)),
    ];
    $start_date = date('Y-m-01', strtotime($start_date.' +1 month'));
}
$last_date_interval = [];
if ($end_date != date('Y-m-t',strtotime($end_date))) {
    $last_date_interval = [
        'start_date' => date('Y-m-01',strtotime($end_date)),
        'end_date' => $end_date,
    ];
}

$slots = getDateSlotInterval('1 month', $start_date, $end_date);
if (!empty($initial_date_interval)) {
   array_unshift($slots, $initial_date_interval);
}
if (!empty($last_date_interval)) {
   $slots[] = $last_date_interval;
}
echo "<pre>";
print_r($slots);
echo "</pre>";


/*
$start_date = '2021-08-10';
$end_date = '2022-02-05';
$start_date = date_create($start_date);
$end_date   = date_create($end_date); // If you want to include this date, add 1 day

echo "<pre>";
print_r($start_date);
echo "</pre>";
echo "<pre>";
print_r($end_date);
echo "</pre>";

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
    $date_slot[$i]["end_date"] = $date_periods[$i+1];
}

echo "<pre>";
print_r($date_slot);
echo "</pre>";
*/
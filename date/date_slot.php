<?php
    function getDateSlotInterval($interval, $start_time, $end_time) : array
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
                $time[$i]['end_date'] = date("Y-m-t", strtotime($start));
            }
        }
        return $time;
    }

    function getMonthlyDateRanges($start_date, $end_date) : array {
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
        return $slots;
    }

$start_date = '2021-08-10';
$end_date = '2022-02-05';
$monthly_date_ranges = getMonthlyDateRanges($start_date, $end_date);
echo "<pre>";
print_r($monthly_date_ranges);
echo "</pre>";
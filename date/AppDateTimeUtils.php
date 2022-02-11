<?php
class AppDateTimeUtils
{
     public static function firstDayNameOfWeek() : string {
        return date("l", strtotime('this week'));
    }

    public static function lastDayNameOfWeek() : string {
        return date("l", strtotime('-1 day next week'));
    }

    public static function weekStartDateOfSpecificDate($date) : string {
        return date("Y-m-d", strtotime("$date this week"));
    }

    public static function getIntervals($start_date, $end_date, $interval_label) : array {
        $start_date_obj = date_create($start_date);
        $end_date_obj = date_create($end_date);
        $interval = DateInterval::createFromDateString($interval_label);
        $date_range = new DatePeriod($start_date_obj, $interval, $end_date_obj);
        $date_periods = [];
        foreach ($date_range as $date) {
            $date_periods[] = $date->format('Y-m-d');
        }
        return $date_periods;
    }


   public static function generateWeeklyDateRanges($start_date, $end_date): array
    {
        $week_start_day_name = self::firstDayNameOfWeek();
        $week_end_day_name = self::lastDayNameOfWeek();
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

        $date_periods = self::getIntervals($start_date, $end_date, '1 week');
        $total_period = count($date_periods);
        $date_slot = [];
        for ($i = 0; $i < $total_period; $i++) {
            if ($i == $total_period - 1) continue;
            $date_slot[$i]["start_date"] = $date_periods[$i];
            $date_slot[$i]["end_date"] = date('Y-m-d', strtotime($date_periods[$i + 1] . '-1 day'));
        }

        if (!empty($initial_date_interval)) {
            array_unshift($date_slot, $initial_date_interval);
        }
        if (!empty($last_date_interval)) {
            $date_slot[] = $last_date_interval;
        }

        return $date_slot;
    }
}
$start_date = '2021-12-01';
$end_date = '2022-02-05';
$date_slot = AppDateTimeUtils::generateWeeklyDateRanges($start_date, $end_date);
echo "<pre>";
print_r($date_slot);
echo "</pre>";
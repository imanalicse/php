<?php
abstract class DateIntervalEnum {
    const DAILY = "daily";
    const WEEKLY = "weekly";
    const MONTHLY = "monthly";
}

class AppDateTimeUtils
{
    public static function firstDayNameOfWeek() : string {
        return date("l", strtotime('this week'));
    }

    public static function lastDayNameOfWeek() : string {
        return date("l", strtotime('-1 day next week'));
    }

    public static function weekStartDateOfSpecificDate($date) : string {
        return date("Y-m-d", strtotime($date ." ". self::firstDayNameOfWeek() . " this week"));
    }

    public static function weekEndDateOfSpecificDate($date) : string {
        return date("Y-m-d", strtotime($date. " ". self::lastDayNameOfWeek() . " this week"));
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

    public static function generateWeeklyDateRanges($start_date, $end_date): array {
        $week_start_date_of_start_date = self::weekStartDateOfSpecificDate($start_date);
        $week_end_date_of_start_date = self::weekEndDateOfSpecificDate($start_date);
        $initial_date_interval = [];
        if ($start_date !== $week_start_date_of_start_date) {
            $initial_date_interval = [
                'start_date' => $start_date,
                'end_date' => $week_end_date_of_start_date,
            ];
            $start_date = date("Y-m-d", strtotime("$week_end_date_of_start_date +1 day"));
        }

        $week_start_date_of_end_date = self::weekStartDateOfSpecificDate($end_date);
        $week_end_date_of_end_date = self::weekEndDateOfSpecificDate($end_date);
        $last_date_interval = [];
        if ($end_date !== $week_end_date_of_end_date) {
            $last_date_interval = [
                'start_date' => $week_start_date_of_end_date,
                'end_date' => $end_date,
            ];
        }

        $date_periods = self::getIntervals($start_date, $end_date, '1 week');
        $date_slot = [];
        for ($i = 0; $i < count($date_periods); $i++) {
            if ($i == count($date_periods) - 1) continue;
            $date_slot[$i]["start_date"] = $date_periods[$i];
            $date_slot[$i]["end_date"] = date('Y-m-d', strtotime($date_periods[$i + 1] . '-1 day'));
        }

        if (!empty($initial_date_interval)) { array_unshift($date_slot, $initial_date_interval); }
        if (!empty($last_date_interval)) { $date_slot[] = $last_date_interval; }
        return $date_slot;
    }

    public static function generateMonthlyDateRanges($start_date, $end_date): array {
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

        $date_periods = self::getIntervals($start_date, $end_date, '1 month');
        $date_slot = [];
        for ($i = 0; $i < count($date_periods); $i++) {
            if ($i == count($date_periods) -1) continue;
            $date_slot[$i]["start_date"] = $date_periods[$i];
            $date_slot[$i]["end_date"] = date('Y-m-t', strtotime($date_periods[$i]));
        }

        if (!empty($initial_date_interval)) array_unshift($date_slot, $initial_date_interval);
        if (!empty($last_date_interval)) $date_slot[] = $last_date_interval;

        return $date_slot;
    }

    public static function generateDateRanges($start_date, $end_date, $interval = DateIntervalEnum::MONTHLY): array
    {
        $date_ranges = [];
        switch ($interval) {
            case DateIntervalEnum::MONTHLY:
            $date_ranges = self::generateMonthlyDateRanges($start_date, $end_date);
            break;
            case DateIntervalEnum::WEEKLY:
            $date_ranges = self::generateWeeklyDateRanges($start_date, $end_date);
            break;
        }
        return $date_ranges;
    }
}
$start_date = '2021-11-16';
$end_date = '2022-02-02';
$date_ranges = AppDateTimeUtils::generateDateRanges($start_date, $end_date, DateIntervalEnum::MONTHLY);
echo "<pre>";
print_r($date_ranges);
echo "</pre>";
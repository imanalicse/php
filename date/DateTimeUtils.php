<?php

class DateTimeUtils {

    public static function firstDayNameOfWeek() : string {
        return date("l", strtotime('this week'));
    }

    public static function lastDayNameOfWeek() : string {
        return date("l", strtotime('-1 day next week'));
    }

    public static function weekStartDateOfSpecificDate($date) : string {
        return date("Y-m-d", strtotime("$date this week"));
    }

    public static function isDate($date, $format = 'Y-m-d H:i:s') : bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

echo "<pre>";
print_r(DateTimeUtils::firstDayNameOfWeek());
echo "</pre>";

echo "<pre>";
print_r(DateTimeUtils::lastDayNameOfWeek());
echo "</pre>";
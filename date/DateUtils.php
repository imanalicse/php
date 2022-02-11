<?php
class dateUtils {
    public static function isDate($date, $format = 'Y-m-d H:i:s') : bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

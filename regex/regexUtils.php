<?php

class regexUtils {

    public function isSlug($slug) : bool
    {
        return preg_match('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $slug);
    }

    public function isAlphaNumeric($string) : bool
    {
        if (ctype_alnum($string)) {
            return true;
        }
        return false;
    }

    // /^[a-zA-Z0-9-_]+$/
    public function isWordCharacterHyphen($value) : bool
    {
        return preg_match('/^[\w-]+$/', $value);
    }

    public function isDate($date, $format = 'dd/mm/yyyy') : bool
    {
        $regex = '';
        switch ($format) {
            case 'dd/mm/yyyy':
                $regex = "/^((0|[1-2])[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/"; // dd/mm/yyyy
            break;

            case 'mm/dd/yyyy':
                $regex = "/^(0[1-9]|1[0-2])\/((0|[1-2])[0-9]|3[0-1])\/[0-9]{4}$/"; // mm/dd/yyyy
            break;

            case 'yy/mm/dd':
                $regex = "/^[0-9]{4}\/(0[1-9]|1[0-2])\/((0|[1-2])[0-9]|3[0-1])$/"; // yy/mm/dd
            break;
        }

        return $regex ? preg_match($regex, $date) : false;
    }
}
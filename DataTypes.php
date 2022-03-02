<?php
class DataTypes
{
    public function parseBooleanValue($val) {
        $new_value = "NOT_BOOLEAN";
        if ($val == "0" || strtolower($val) == 'false') {
            $new_value = false;
        }
        if ($val == "1" || strtolower($val) == 'true') {
            $new_value = true;
        }
        return $new_value;
    }

    /*
     * filter_var(true, FILTER_VALIDATE_BOOLEAN); // true
        filter_var('true', FILTER_VALIDATE_BOOLEAN); // true
        filter_var(1, FILTER_VALIDATE_BOOLEAN); // true
        filter_var('1', FILTER_VALIDATE_BOOLEAN); // true
        filter_var('on', FILTER_VALIDATE_BOOLEAN); // true
        filter_var('yes', FILTER_VALIDATE_BOOLEAN); // true

        filter_var(false, FILTER_VALIDATE_BOOLEAN); // false
        filter_var('false', FILTER_VALIDATE_BOOLEAN); // false
        filter_var(0, FILTER_VALIDATE_BOOLEAN); // false
        filter_var('0', FILTER_VALIDATE_BOOLEAN); // false
        filter_var('off', FILTER_VALIDATE_BOOLEAN); // false
        filter_var('no', FILTER_VALIDATE_BOOLEAN); // false
        filter_var('abc', FILTER_VALIDATE_BOOLEAN); // false
        filter_var('', FILTER_VALIDATE_BOOLEAN); // false
        filter_var(null, FILTER_VALIDATE_BOOLEAN); // false
     *
     */
    public static function convertToBoolean($value) {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

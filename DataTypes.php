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
}

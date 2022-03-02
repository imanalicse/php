<?php
//namespace App;
class FunctionsUtils
{
    /**
     * $url = "https://olive.doyour.events/b/org/reports/orders?search=Ishak&search_event=10575&startDate=2022-01-31%2000%3A00%3A00&endDate=2022-03-01%2023%3A59%3A59&search_status=1";
     */
    public static function getQueryParamValue($url, $name) {
        preg_match("/[\?&]".$name."=([^&#]*)/", $url, $match);
        if(!empty($match) && isset($match[1])){
            return $match[1];
        }
        return '';
    }

    public static function getQueryParamsAsArray($url) {
        $query_string = parse_url($url, PHP_URL_QUERY);
        parse_str($query_string, $query_string_array);
        return $query_string_array;
    }

    public static function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            random_int(0, 65535),
            random_int(0, 65535),
            // 16 bits for "time_mid"
            random_int(0, 65535),
            // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
            random_int(0, 4095) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            random_int(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535)
        );
    }
}

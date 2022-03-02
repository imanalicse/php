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
}

<?php
namespace App\Utils;

class FunctionsUtils
{
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

    public static function redirect($url) {
        header('Location: '.$url);
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

   public static function shortenNumberFormat($n, int $precision = 1) : string
    {
        $one_thousand = 1000;
        $one_million  = 1000000;
        $one_billion  = 1000000000;
        $one_trillion = 1000000000000;

        $number_format = $n;
        $suffix = '';

        if ($n >= $one_thousand && $n < $one_million) {
            $number_format = number_format($n / $one_thousand, $precision);
            $suffix = 'K';
        }
        elseif ($n >= $one_million && $n < $one_billion) {
            $number_format = number_format($n / $one_million, $precision);
            $suffix = 'M';
        }
        elseif ($n >= $one_billion && $n < $one_trillion) {
            $number_format = number_format($n / $one_billion, $precision);
            $suffix = 'B';
        }
        elseif ($n >= $one_trillion) {
            $number_format = number_format($n / $one_trillion, $precision);
            $suffix = 'T';
        }
        return $number_format . $suffix;
    }

    public static function shortenNumberFormatPlus($n) : string
    {
        $one_thousand = 1000;
        $one_million  = 1000000;
        $one_billion  = 1000000000;
        $one_trillion = 1000000000000;

        $number_format = $n;
        $suffix = '';

        if ($n >= $one_thousand && $n < $one_million) {
            $number_format = floor( $n / $one_thousand);
            $suffix = 'K+';
        }
        elseif ($n >= $one_million && $n < $one_billion) {
            $number_format = floor($n / $one_million);
            $suffix = 'M+';
        }
        elseif ($n >= $one_billion && $n < $one_trillion) {
            $number_format = floor($n / $one_billion);
            $suffix = 'B+';
        }
        elseif ($n >= $one_trillion) {
            $number_format = floor($n / $one_trillion);
            $suffix = 'T+';
        }
        return $number_format . $suffix;
    }
}

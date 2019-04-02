<?php
class CurlExecutor
{
    static function execute($url, $method = "GET", $posts = null, $gets = null, $headers = null, Closure $closure = null)
    {
        $ch = curl_init();
        if ($gets != null) {
            $url .= "?" . (is_array($gets) ? self::makeRawQuery($gets) : $gets);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($posts != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($posts) ? self::makeRawQuery($posts) : $posts);
        }
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
        } else if ($method == "HEAD") {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        } else if ($method == "PUT" || $method == "BATCH" || $method == "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        if (!is_null($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!is_null($closure)) {
            $closure($ch);
        }
        $response = curl_exec($ch);
        if ((curl_errno($ch) == 60)) {
            /* Invalid or no certificate authority found - Retrying without ssl */
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
        }
        /* If you want to retry on failed status code */
        $retryCodes = array('401', '403', '404');
        $retries = 0;
        $retryCount = 3;
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (in_array($httpStatus, $retryCodes)) {
            do {
                $response = curl_exec($ch);
                $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            } while (in_array($httpStatus, $retryCodes) && (++$retries < $retryCount));
        }
        if (curl_errno($ch)) {
            $response = curl_error($ch);
        }
        curl_close($ch);
        return array(
            "code" => $httpStatus,
            "response" => $response
        );
    }

    static function makeRawQuery($data, $keyPrefix = "")
    {
        $query = "";
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (strlen($keyPrefix) > 0) {
                    $keyPrefixDummy = $keyPrefix . "[" . $key . "]";
                } else {
                    $keyPrefixDummy = $key;
                }
                $query = $query . self::makeRawQuery($value, $keyPrefixDummy);
            } else {
                if (strlen($keyPrefix) > 0) {
                    $key = $keyPrefix . "[" . $key . "]";
                }
                $query .= $key . "=" . rawurlencode($value) . "&";
            }
        }
        return rtrim($query, "&");
    }

    static function prettyPrint($o)
    {
        echo "<pre>";
        print_r($o);
        echo "</pre>";
    }
}
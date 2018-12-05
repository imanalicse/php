<?php
function addHttps($url)
{
    $return_url = $url;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $return_url = preg_replace("/http:/", "https:", $url);
    }
    return $return_url;
}

function saveLog($path = "", $file = "", $data = array(), $logType = 'debug')
{
    if ($this->request->params['action'] == "saveLog") {
        throw new NotFoundException;
    }

    $path = trim($path);
    $file = trim($file);

    $pattern = '/[^a-zA-Z0-9_ - \.\/]/i';
    $replacement = '/';

    if (!empty($path)) {
        $path = preg_replace($pattern, $replacement, str_replace(" ", "_", $path));

    } else {
    }

    $path = $_SERVER['HTTP_HOST'] . "/" . trim($path, "/");
    $path = LOGS . trim($path, "/") . "/";

    if (empty($file)) {
        $file = "log";
    }
    $pattern = '/[^a-zA-Z0-9_-]/i';
    $replacement = '_';
    $file = preg_replace($pattern, $replacement, $file);

    $key = preg_replace($pattern, $replacement, $path . $file);

    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    try {
        try {
            Log::config($key, [
                'className' => 'File',
                'path' => $path,
                'levels' => [$logType],
                'file' => $file
            ]);
        } catch (\Exception $e) {

        }

        Log::write($logType, $data, [$key]);
        Log::write($logType, "\n\n", [$key]);


    } catch (\Exception $e) {

    }

}
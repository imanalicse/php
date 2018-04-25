<?php

class Curl
{
    public $curl;
    private $headers = array();

    public function __construct($base_url = null)
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('The cURL extensions is not loaded, make sure you have installed the cURL extension: https://php.net/manual/curl.setup.php');
        }

        $this->curl = curl_init();
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setHeader("Content-Type", "application/json");
    }

    public function post($url, $data = array()) {
        $data = json_encode($data);
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->setOpt(CURLOPT_URL, $url);
        $result = curl_exec($this->curl);
        return $result;
    }

    public function get($url){
        $this->setOpt(CURLOPT_URL, $url);
        $result = curl_exec($this->curl);
        return $result;
    }

    public function exec() {
        curl_exec($this->curl);
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    public function setOpt($option, $value)
    {
        return curl_setopt($this->curl, $option, $value);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
        return $this;
    }

//    public function postPrv($url, $data = array())
//    {
//        $data = json_encode($data);
//        $ch = curl_init($url);
//        # Setup request to send json via POST.
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
//        # Return response instead of printing.
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        // Get data from https url
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        # Send request.
//        $result = curl_exec($ch);
//        curl_close($ch);
//        return $result;
//    }
//
//    public function getPrev($url, $headers){
//        $ch = curl_init($url);
//        # Setup request to send json via POST.
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        # Return response instead of printing.
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        # Get data from https url
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        # Send request.
//        $result = curl_exec($ch);
//        curl_close($ch);
//        return $result;
//    }
}
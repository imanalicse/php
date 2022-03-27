<?php
namespace App\Utils;

class Session
{
    public static function read($key) {
        session_start();
        if (!empty($key)) {
           return $_SESSION[$key];
        }
    }

    public static function write($key, $value) {
        session_start();
        if (!empty($key)) {
            $_SESSION[$key] = $value;
        }
    }

    public static function delete($key) {
        session_start();
        if (!empty($key) && isset($_SESSION[$key])) {
           unset($_SESSION[$key]);
        }
    }

}

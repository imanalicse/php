<?php
namespace App\Utils;

class Session
{
    public static function read($key) {
        self::session_start();
        if (!empty($key)) {
           return $_SESSION[$key];
        }
    }

    public static function write($key, $value) {
        self::session_start();
        if (!empty($key)) {
            $_SESSION[$key] = $value;
        }
    }

    public static function delete($key) {
        self::session_start();
        if (!empty($key) && isset($_SESSION[$key])) {
           unset($_SESSION[$key]);
        }
    }

    public static function session_start() : void {
        if (session_id() == '' || !isset($_SESSION)) {
            session_start();
        }
    }
}

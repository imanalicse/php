<?php

namespace App\MySQL;

class DbConnection
{
    public static function connect($db_host, $db_username, $db_password, $database) : object
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $connection = new \mysqli($db_host, $db_username, $db_password, $database);
        if ($connection->connect_errno) {
            echo "Failed to connect to MySQL: " . $connection->connect_error;
            exit();
        }
        return $connection;
    }
}
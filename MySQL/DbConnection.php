<?php

namespace App\MySQL;

class DbConnection
{
    public static function connect($db_host, $db_username, $db_password, $database) : object
    {
        $connection = mysqli_connect($db_host, $db_username, $db_password, $database);
        if ($connection->connect_errno) {
            echo "Failed to connect to MySQL: " . $connection->connect_error;
            exit();
        }
        return $connection;
    }
}
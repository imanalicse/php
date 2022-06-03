<?php
namespace App\DateUtil;
use App\MySQL\DbConnection;

class Test
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

    public static function timeZoneTest()
    {
        $connection = self::connect("localhost", "root", "", "webapp_diywebsite_net_au");

        $sql = "SELECT id, scheduled_at, timezone FROM shared_s3v3_org_event_compaigns where id = 96383";
        $result = $connection->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row) {
            echo "<pre>";
            print_r($row);
            echo "</pre>";
            $timezone = $row['timezone'];
            $scheduled_at = $row['scheduled_at'];
            $scheduled_date_time = date('Y-m-d H:i:s', $row['scheduled_at']);
            echo "<pre>";
            print_r($scheduled_date_time);
            echo "</pre>";

        }

    }
}

Test::timeZoneTest();




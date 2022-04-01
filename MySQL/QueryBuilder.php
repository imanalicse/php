<?php
namespace App\MySQL;

use App\MySQL\DbConnection;
use Matrix\Exception;

class QueryBuilder
{
    private object $connection;

    public function __construct() {
        $this->connection = DbConnection::connect("localhost", "root", "", "imanalicse");
    }

    public function fetchAll($query, int $mode = MYSQLI_NUM) {
        $result = $this->connection->query($query);
        $rows = $result->fetch_all($mode);
        $this->closeConnection();
        return $rows;
    }

    public function insert(string $table_name, array $data) {
        try {
            $field_string = implode(',', array_keys($data));
            $values = array_map(function ($value, $key) {
                return "'".$value."'";
            }, array_values($data), array_keys($data));
            $value_string = implode(',', $values);
            $sql = "INSERT INTO $table_name ( $field_string ) VALUE ( $value_string )";
            $result = $this->connection->query($sql);
        } catch (Exception $exception) {
            $result =  $exception->getMessage();
        }

        return $result;
    }

    public function insertRaw($query) {
        return $this->connection->query($query);
    }

    public function closeConnection() {
        $this->connection->close();
    }
}
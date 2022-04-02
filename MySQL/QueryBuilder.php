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

    public function get(string $table) : QueryBuilder {
        $this->fetch_all_data = "SELECT * FROM $table";
        return $this;
    }

    public function condition(string $condition) : QueryBuilder {
        $this->fetch_all_data .= " WHERE $condition";
        return $this;
    }

    public function orderBy(string $orderBy): QueryBuilder {
        $this->fetch_all_data .= " ORDER BY $orderBy";
        return $this;
    }

    public function limit(int $limit): QueryBuilder {
        $this->fetch_all_data .= " LIMIT $limit";
        return $this;
    }

     public function findAll() {
        $result = $this->connection->query($this->fetch_all_data);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->closeConnection();
        return $rows;
    }

    public function find($print_query = false) {
        $this->fetch_all_data .= " LIMIT 1";
        if ($print_query) {
            echo $this->fetch_all_data;
        }
        $result = $this->connection->query($this->fetch_all_data);
        $rows = $result->fetch_assoc();
        $this->closeConnection();
        return $rows;
    }

    public function fetchAll($query, int $mode = MYSQLI_ASSOC) {
        $result = $this->connection->query($query);
        $rows = $result->fetch_all($mode);
        $this->closeConnection();
        return $rows;
    }

    public function insert(string $table_name, array $data) {
        $result = 0;
        try {
            $field_string = implode(',', array_keys($data));
            $values = array_map(function ($value, $key) {
                return "'".$value."'";
            }, array_values($data), array_keys($data));
            $value_string = implode(',', $values);
            $sql = "INSERT INTO $table_name ( $field_string ) VALUE ( $value_string )";
            if ($this->connection->query($sql)) {
                $result =$this->connection->insert_id;
            }
        } catch (Exception $exception) {
            $result =  $exception->getMessage();
        }

        return $result;
    }

    public function insertRaw($query) {
        return $this->connection->query($query);
    }

    protected function closeConnection() {
        $this->connection->close();
    }
}
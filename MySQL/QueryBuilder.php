<?php
namespace App\MySQL;

use App\MySQL\DbConnection;
use Matrix\Exception;

class QueryBuilder
{
    private object $connection;
    private string $select_query;
    private string $update_query;

    public function __construct() {
        $this->connection = DbConnection::connect("localhost", "root", "", "imanalicse");
    }

    public function get(string $table) : QueryBuilder {
        $this->select_query = "SELECT * FROM $table";
        return $this;
    }

    public function where($conditions = null) : QueryBuilder {
        $new_conditions = [];
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $comparison_operator = strpos($key, ">") ? "" : " = ";
                $new_conditions[] = $key .' '.$comparison_operator. "'".$value ."'";
            }
            $condition_string = implode(' AND ', $new_conditions);
            $this->select_query .= " WHERE $condition_string";
        }
        return $this;
    }

    public function order(string $orderBy): QueryBuilder {
        $this->select_query .= " ORDER BY $orderBy";
        return $this;
    }

    public function limit(int $limit): QueryBuilder {
        $this->select_query .= " LIMIT $limit";
        return $this;
    }

     public function findAll($print_query = false) {
        if ($print_query) {
            echo $this->select_query;
        }
        $result = $this->connection->query($this->select_query);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->closeConnection();
        return $rows;
    }

    public function find($print_query = false) {
        $this->select_query .= " LIMIT 1";
        if ($print_query) {
            echo $this->select_query;
        }
        $result = $this->connection->query($this->select_query);
        $rows = $result->fetch_assoc();
        $this->closeConnection();
        return $rows;
    }

    /**
     * @throws Exception
     */
    public function insert(string $table_name, array $data): int {
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
        }
        catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return $result;
    }

    public function update(string $table_name) : QueryBuilder {
        $this->update_query = "UPDATE $table_name";
        return $this;
    }

    public function setUpdateData(array $data) : QueryBuilder {
        $new_data = [];
        foreach ($data as $key => $datum) {
            $new_data[] = "$key = "."'" .$datum ."'";
        }
        $set_data = implode(', ', $new_data);
        $this->update_query .= " SET $set_data";
        return $this;
    }

    public function setUpdateCondition(array $conditions) : QueryBuilder {
        $new_conditions = [];
        foreach ($conditions as $key => $datum) {
            $new_conditions[] = "$key = "."'" .$datum ."'";
        }
        $where_data = implode('AND ', $new_conditions);
        $this->update_query .= "  WHERE $where_data";
        return $this;
    }

    public function executeUpdate($debug = false): int {
        try {
            if ($debug) {
                echo $this->update_query;
            }
            if ($this->connection->query($this->update_query)) {
                return 1;
            }
        }
        catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
        return 0;
    }

    public function insertRaw($query) {
        return $this->connection->query($query);
    }

    protected function closeConnection() {
        $this->connection->close();
    }
}
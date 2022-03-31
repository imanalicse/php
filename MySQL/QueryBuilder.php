<?php
namespace App\MySQL;

use App\MySQL\DbConnection;

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

    public function insert($query) {
        return $this->connection->query($query);
    }

    public function closeConnection() {
        $this->connection->close();
    }
}
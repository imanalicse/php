<?php

class ImportHandler
{
    var $MAP_COLUMN_TABLE = [
        "First Name"    => "first_name",
        "Last Name"     => "last_name",
        "Email"         => "email",
        "Fee"           => "fee",
    ];

    var $DATA_TYPES = [
        "Fee" => 'is_numeric'
    ];

    function assoc2indexedMulti($arr) {
        $indArr = array();
        foreach($arr as $val) {
            if(is_array($val)) {
                $indArr[] = $this->assoc2indexedMulti($val);
            } else {
                $indArr[] = $val;
            }
        }
        return $indArr;
    }

    public function saveImportedData($excelData) {

        $db_host = 'localhost';
        $db_user = 'root';
        $db_password = '';
        $db_name = 'sampledatabase';
        $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

//        $query = "INSERT INTO students (first_name, last_name, email)
//                    VALUES
//                    ('Iman', 'Ali','iman@gmail.com'),
//                    ('fdsa', 'fdasf','ewew@gmail.com')
//                    ";

        $excelData = $this->assoc2indexedMulti($excelData);
        $total_items = count($excelData);
        $table_column_list = [];
        $table_value_list = [];
        for ($i = 1; $i < $total_items; $i++) {
            $prepare_items = [];
            $exclude = false;
            for ($j = 0; $j < count($excelData[$i]); $j++) {
                $column_name = trim($excelData[0][$j]);
                if (!key_exists($column_name, $this->MAP_COLUMN_TABLE)) {
                    continue;
                }

                $value = trim($excelData[$i][$j]);

                // Check data type
                if (key_exists($column_name, $this->DATA_TYPES)) {
                    if (!$this->DATA_TYPES[$column_name]($value)) {
                        $exclude = true;
                    }
                }

                $field = $this->MAP_COLUMN_TABLE[$column_name];
                $prepare_items[] = "'".$value."'";
                if ($i == 1) { $table_column_list[] = $field; }
            }
             if ($exclude) {
                 //wrong data type here
                 //$prepare_items
                 continue;
             }
            $table_value_list[] = '(' . implode(', ', $prepare_items) . ')';
        }

        if (!empty($table_value_list)) {
            $columns = implode(', ', $table_column_list);
            $values = implode(', ', $table_value_list);
            $query = "INSERT INTO students (".$columns.")
                    VALUES
                    $values
                    ";
            echo "<pre>";
            print_r($query);
            echo "</pre>";
            try {
                $mysqli->query($query);
            } catch (Exception $exception) {
                echo "<pre>";
                print_r($exception->getMessage());
                echo "</pre>";
            }
            $mysqli->close();
        }
    }
}
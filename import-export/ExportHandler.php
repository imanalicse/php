<?php

class ExportHandler
{

    function exportCSV($data, $filename = 'students.csv')
    {
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");

        $result = "";
        if (count($data) > 0) {
            if (isset($data[0])) {
                $keys = array_keys($data[0]);
                $first = true;
                foreach ($keys as $key) {
                    if (!$first) {
                        $result .= ",";
                    }
                    $key = str_replace("}", "|", $key);
                    $key = str_replace("{", "|", $key);
                    $key = str_replace(",", "|", $key);
                    $key = trim($key);
                    $result .= $key;

                    $first = false;
                }
            }

            foreach ($data as $row) {
                $result .= "\n";
                $first = true;
                foreach ($row as $col) {
                    if (!$first) {
                        $result .= ",";
                    }
                    $col = str_replace("}", "|", $col);
                    $col = str_replace("{", "|", $col);
                    // $col = str_replace(",", "\/", $col);
                    $col = trim($col);
                    $result .= '"' . $col . '"';//"{$col}";
                    $first = false;
                }
            }
        }
        echo $result;
    }

    function setCsvHeader($download_file_path){
        $fd = fopen ($download_file_path, "w");
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=".$fd);
        header("Pragma: no-cache");
        header("Expires: 0");
        return $fd;
    }

}
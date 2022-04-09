<?php
include_once "ExportHandler.php";

$csv_data[] = array(
    "Order ID"=> "100",
    "Customer ID"=> "124KD",
    "Customer Name" => "John Doe",
    "Amount" => 100
);

$handler = new ExportHandler();
$handler->exportCSV($csv_data, 'order.csv');
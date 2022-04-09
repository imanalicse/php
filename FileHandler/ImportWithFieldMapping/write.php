<?php
require '../../vendor/autoload.php';

use App\FileHandler\SpreadsheetHandler;
use App\Utils\Session;


$arrayExcelData = SpreadsheetHandler::writeSpreadsheet([], 'contacts.csv');
echo "<pre>";
print_r($arrayExcelData);
echo "</pre>";
die("dddd");
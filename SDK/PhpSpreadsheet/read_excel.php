<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;


$inputFileName = __DIR__ . '/SampleFile/example1.xls';
$spreadsheet = IOFactory::load($inputFileName);

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
echo '<pre>';
echo print_r($sheetData);
echo '</pre>';

<?php
namespace App\ImportExport;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use App\FileHandler\FileHandler;

class SpreadsheetHandler
{
    public static function readSpreadsheet($excelFilePath, $active_sheet = 0 ) : array {
        $extension = pathinfo($excelFilePath, PATHINFO_EXTENSION);
        $reader_obj = '';
        switch ($extension) {
          case 'csv':
            $reader_obj = new Csv();
            break;
          case 'xlsx':
            $reader_obj = new Xlsx();
            break;
          case 'xls':
            $reader_obj = new Xls();
            break;
        }
        $reader_obj->setReadDataOnly(true);
        $spreadsheet = $reader_obj->load($excelFilePath);
        $active_sheet = $spreadsheet->getActiveSheet();
        //$sheet_data = $spreadsheet->getActiveSheet()->toArray();
        $highestRow = $active_sheet->getHighestRow();
        $highestColumn = $active_sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $file_data = [];
        for ($row = 1; $row <= $highestRow; ++$row) {
            $first_field = $active_sheet->getCellByColumnAndRow(0, $row)->getCalculatedValue();
            if(!empty($first_field)) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $file_data[$row][$col] = $active_sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                }
            }
        }
        return $file_data;
    }

    public static function writeSpreadsheet($data_array, $file_name, $activesheet = 0)
    {
        $spreadsheet = new Spreadsheet();
        $active_sheet = $spreadsheet->getActiveSheet();

        //$active_sheet->setCellValueByColumnAndRow()
        $active_sheet->setCellValue('A1', 'First Name');
        $active_sheet->setCellValue('B1', 'Last Name');
        $active_sheet->setCellValue('C1', 'Email Address');
        $active_sheet->setCellValue('D1', 'Custom field 1');

        $active_sheet->setCellValue('A2', 'Iman');
        $active_sheet->setCellValue('B2', 'Ali');
        $active_sheet->setCellValue('C2', 'iman@bitmascot.com');
        $active_sheet->setCellValue('D2', 'custom value 1');

        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        switch ($extension) {
          case 'csv':
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            break;
          case 'xlsx':
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            break;
          case 'xls':
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
            break;
        }
        $writer->save($file_name);
        $file_handler = new FileHandler();
        $file_handler->download($file_name);
        exit();
    }
}


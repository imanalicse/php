<?php
namespace App\ImportExport;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
}


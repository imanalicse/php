<?php
require '../../../vendor/autoload.php';

use App\FileHandler\SpreadsheetHandler;
use App\Utils\Session;
use App\FileHandler\FileHandler;
use App\FileHandler\ImportWithFieldMapping\ImporterUtils;

$post_data = $_POST;
$import_utils = new ImporterUtils();
$response = $import_utils->importFile($_FILES);
echo json_encode($response);
/*
$response = [
    'status'=> false,
    'msg' => 'Please upload file'
];
if (!empty($_FILES)) {
    $upload_file_path = __DIR__ . '/uploads/';
    $file = $_FILES["file_name"];
    $file_handler = new FileHandler();
    $result = $file_handler->uploadFile($file, $upload_file_path, ['xls','xlsx']);
    if ($result['is_success']) {
        $uploadFileName = $file_handler->_uploadimgname;
        $excelFilePath = $upload_file_path . DIRECTORY_SEPARATOR . $uploadFileName;
        $arrayExcelData = SpreadsheetHandler::readSpreadsheet($excelFilePath, 0);
        if ($arrayExcelData) {
            if (count($arrayExcelData) > 1) {
                //$import_file_id = $this->getComponent('AttendeeImport.Importer')->saveImportFile($uploadFileName);
                $import_file_id = 2;
                $process_students = [
                    'excel_data' => $arrayExcelData,
                    'import_file_id' => $import_file_id
                ];
                Session::write('import_students', $process_students);
                $response['status'] = true;
                $response['msg'] = "Total number of processed students: " . (count($arrayExcelData) - 1);
            } else {
                $response['status'] = false;
                $response['msg'] = 'Data missing. Please fill up data properly.';
            }
        } else {
            $response['status'] = false;
            $response['msg'] = 'File is empty. Please fill up data.';
        }
    } else {
        $response['status'] = false;
        $response['msg'] = $result['message'];
    }
}
echo json_encode($response);
die();
*/

<?php
namespace App\FileHandler\ImportWithFieldMapping;

use App\Utils\Session;
use App\MySQL\QueryBuilder;
use App\FileHandler\FileHandler;
use App\FileHandler\SpreadsheetHandler;

class ImporterUtils {

    protected $list_table_columns = [
        "student_id",
        "first_name",
        "last_name",
        "email_address"
    ];

    protected $db_field_aliasing = [
        "student_id" => "Student ID",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "email_address" => "Email Address",
        "address_line_1" => "Address 1"
    ];

    const ITEM_PER_PAGE = 10;

    protected function getRequiredFields() : array {
        return ["student_id"];
    }

    protected function excludeDbFields() : array {
        return ['id', 'user_id', 'created', 'modified'];
    }

    public function import() {

    }

    public function getSampleFileInfo() {
        $sample_file_info = [];
        $file_name = 'sample.xlsx';
        $file_path = dirname(__FILE__). DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $file_name;
        if (file_exists($file_path)) {
            $sample_file_info = [
                'file_name' => $file_name,
                'file_path' => $file_path
            ];
        }
        return $sample_file_info;
    }

    public function downloadSampleFile() {
        $sample_file_info = $this->getSampleFileInfo();
        if (!empty($sample_file_info) && file_exists($sample_file_info['file_path'])) {
            $file_handler = new FileHandler();
            $file_handler->download($sample_file_info['file_path']);
        }
        exit();
    }

    public function importFile($files) : array {
        $response = [
            'status'=> false,
            'msg' => 'Please upload file'
        ];
        if (!empty($files)) {
            $upload_file_path = __DIR__ . '/uploads/';
            $file = $files["file_name"];
            $file_handler = new FileHandler();
            $result = $file_handler->upload($file, $upload_file_path, ['xls','xlsx']);
            if ($result['is_success']) {
                $uploadFileName = $file_handler->uploaded_file_name;
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
        return $response;
    }

    public function fieldMapping() : array {
        $import_students = $this->getImportedSessionData();
        $db_field_names = $this->getStudentFieldNames();
        $required_fields = $this->getRequiredFields();
        $mapped_data = $this->getKeyValueMappedData();
        $db_field_aliasing = $this->db_field_aliasing;
        return [
            "import_students" => $import_students,
            "db_field_names" => $db_field_names,
            "required_fields" => $required_fields,
            "mapped_data" => $mapped_data,
            "db_field_aliasing" => $db_field_aliasing,
        ];
    }

    protected function getImportedSessionData() {
        $import_students = Session::read('import_students');
        $list_heading = $import_students['excel_data'][1];
        $list_data = array_slice($import_students['excel_data'], 1);
        $data['list_heading'] = $list_heading;
        $data['list_data'] = $list_data;
        $data['import_file_id'] = $import_students['import_file_id'];
        return $data;
    }

    private function getStudentFieldNames() {
        $query_builder = new QueryBuilder();
        $sql = "DESCRIBE students;";
        $result = $query_builder->rawExecute($sql);
        $table_schema = $result->fetch_all(MYSQLI_ASSOC);
        $db_field_names = array_column($table_schema, 'Field');
        return array_diff($db_field_names, $this->excludeDbFields());
    }


    public function saveFieldMapping($post_data) {

        $response = ['status'=> false, 'msg'=> 'Something went wrong. please try again'];
        $this->validateRequiredFields($post_data);
        if (!empty($post_data)) {
            $query_builder = new QueryBuilder();
            $query_builder->delete("import_mapper");
            foreach ($post_data as $db_field => $excel_field_label) {
                $mapped_arr = [
                    'db_field' => $db_field,
                    'excel_field_label' => $excel_field_label,
                ];
                $query_builder = new QueryBuilder();
                $query_builder->insert("import_mapper", $mapped_arr);
            }
            $response = ['status' => true, 'msg' => 'Mapped data save successfully'];
        }
        return $response;
    }

    protected function validateRequiredFields($post_data) {
        $required_fields = $this->getRequiredFields();
        //Required field error for field mapping
        $required_field_errors = [];
        foreach ($post_data as $db_field => $excel_label_field) {
            if (in_array($db_field, $required_fields) && empty($excel_label_field)) {
                $required_field_errors[] = $db_field;
            }
        }

        if (!empty($required_field_errors)) {
            return $response = ['status' => false, 'msg' => implode(', ', $required_field_errors) . ' are mandatory'];
        }

        $required_field_errors = $this->getRequiredFieldErrorsForListingData($post_data);

        if (!empty($required_field_errors)) {
           return $response = ['status' => false, 'msg' => implode(', ', $required_field_errors) . ' are mandatory'];
        }

        return ['status' => false, 'msg' => implode(', ', $required_field_errors) . ' are mandatory'];
    }

    protected function getRequiredFieldErrorsForListingData($post_data) {
        $import_session_data = $this->getImportedSessionData();
        $list_heading = $import_session_data['list_heading'];
        $list_data = $import_session_data['list_data'];

        $required_fields = $this->getRequiredFields();

        //Finding required field index
        $required_field_errors = [];
        $required_field_index = [];
        if (!empty($required_fields)) {
            foreach ($required_fields as $required_field) {
                if (array_key_exists($required_field, $post_data)) {
                    foreach ($post_data as $db_field => $post_excel_label) {
                        foreach ($list_heading as $index => $heading_label) {
                            if ($post_excel_label == $heading_label && $db_field == $required_field) {
                                $required_field_index[$db_field] = $index;
                            }
                        }
                    }
                } else {
                    $required_field_errors[] = $required_field;
                }
            }
        }

        //Finding required field error while data missing
        foreach ($required_field_index as $required_field => $index) {
            $required_success = true;
            foreach ($list_data as $list_item) {
               if (!isset($list_item[$index]) || empty($list_item[$index])) {
                  $required_success = false;
               }
            }
            if (!$required_success) {
                $required_field_errors[] = $required_field;
            }
        }
        return $required_field_errors;
    }

    public function itemListing() {
        $import_session_data = $this->getImportedSessionData();
        $list_heading = $import_session_data['list_heading'];
        $list_data = $import_session_data['list_data'];
        $total_record = count($list_data);

        $mapped_data = $this->getKeyValueMappedData();
        $listing_mapped_index = [];
        if (!empty($mapped_data)) {
            foreach ($mapped_data as $db_field => $excel_field_label) {
                foreach ($list_heading as $index => $heading_label) {
                    if ($excel_field_label == $heading_label && in_array($db_field, $this->list_table_columns)) {
                        $listing_mapped_index[$db_field] = $index;
                    }
                }
            }
        }

        Session::write('listing_mapped_index', $listing_mapped_index);

        $item_per_page = self::ITEM_PER_PAGE;
        $list_data = array_slice($list_data, 0, $item_per_page);

        $pagination_setting = [
            'total_record' => $total_record,
            'item_per_page' => $item_per_page,
            'page' => 1
        ];
        return [
            "pagination_setting" => $pagination_setting,
            "listing_mapped_index" => $listing_mapped_index,
            "list_heading" => $list_heading,
            "list_data" => $list_data
        ];
    }

    public function paging($post_data) : array {
         $listing_mapped_index = [];
         $item_per_page = $post_data['item_per_page'];
         $page = $post_data['page'];
         if ($page <= 0) {
            $page = 1;
         }
        $offset = ($page - 1) * $item_per_page;
        $import_session_data = $this->getImportedSessionData();
        $list_data = $import_session_data['list_data'];
        $list_data = array_slice($list_data, $offset, $item_per_page);
        $listing_mapped_index = Session::read('listing_mapped_index');
        return [
            "listing_mapped_index" => $listing_mapped_index,
            "list_data" => $list_data,
        ];
    }

    public function saveImportData() {
        $response = ['status' => false, 'msg' => 'Saving in progress'];
        echo json_encode($response);
        die();
    }

    private function getKeyValueMappedData() : array {
        $query_builder = new QueryBuilder();
        $rows =   $query_builder->get("import_mapper")->findAll();
        $mapped_data = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $mapped_data[$row["db_field"]] = $row["excel_field_label"];
            }
        }
        return $mapped_data;
    }
}
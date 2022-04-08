<?php
namespace App\ImportExport\ImportWithFieldMapping;

use App\Utils\Session;
use App\MySQL\QueryBuilder;

class ImporterUtils {

    public $layout;
    public $session;

    protected $list_table_columns = [
        "uuid",
        "event_name"
    ];

    protected $db_field_aliasing = [
        "uuid" => "UUID",
        "event_name" => "Event Name"
    ];

    const ITEM_PER_PAGE = 10;

    protected function getRequiredFields() {
        return ["student_id"];
    }

    protected function excludeDbFields() : array {
        return ['id', 'created', 'modified'];
    }

    public function import() {
        $sample_file_info = $this->getComponent('AttendeeImport.Importer')->getSampleFileInfo();
        $this->set('sample_file_info', $sample_file_info);
    }

    public function downloadSampleFile() {
        $sample_file_info = $this->getComponent('AttendeeImport.Importer')->getSampleFileInfo();
        if (!empty($sample_file_info) && file_exists($sample_file_info['file_path'])) {
            $this->getComponent('FileHandler')->download($sample_file_info['file_path']);
        }
        exit();
    }

    public function getPopupInitBody() {
        $sample_file_info = $this->getComponent('AttendeeImport.Importer')->getSampleFileInfo();
        $this->set('sample_file_info', $sample_file_info);
    }

    public function importFile() {
        Configure::write('debug',false);
        $response = [
            'status'=> false,
            'msg' => 'Please upload file'
        ];
        if ($this->request->is('post') && $this->request->getData()) {
            ini_set('memory_limit', '1024M');
            $post_data = $this->request->getData();
            $currentTime = FrozenTime::now()->toUnixString();
            $orgId = $this->getOrgId();
            $orgHash = $this->getOrgTokenMd5($orgId);
            $uploadDirRoot = $this->getOrgUploadRootDirectory();
            $file_relative_path = $orgHash. DS ."import-attendee". DS .$currentTime;
            $upload_file_path = $uploadDirRoot. DS .$file_relative_path;
            $file = $_FILES["file_name"];
            $result = $this->getComponent('FileHandler')->uploadFileNew($file, $upload_file_path, ['xls','xlsx']);
            if ($result['is_success']) {
                $uploadFileName = $this->getComponent('FileHandler')->_uploadimgname;
                $excelFilePath = $upload_file_path . DS . $uploadFileName;
                $arrayExcelData = $this->getComponent('AttendeeImport.ExcelHandler')->read_excel($excelFilePath, 0);
                if ($arrayExcelData) {
                    if (count($arrayExcelData) > 1) {
                        $import_file_id = $this->getComponent('AttendeeImport.Importer')->saveImportFile($uploadFileName);
                        $process_students = [
                            'excel_data' => $arrayExcelData,
                            'import_file_id' => $import_file_id
                        ];
                        $this->session->write('import_students', $process_students);

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


    public function saveFieldMapping() {
        $response = ['status'=> false, 'msg'=> 'Please submit data'];
        if ($this->request->is("post") && $this->request->getData()) {
            $response = ['status'=> false, 'msg'=> 'Something went wrong. please try again'];
            $post_data = $this->request->getData();
            $this->validateRequiredFields($post_data);

            $this->ImportMapper = $this->getDbTable("AttendeeImport.ImportMapper");
            if (!empty($post_data)) {
                $mapped_arr = [];
                foreach ($post_data as $db_field => $excel_field_label) {
                    $mapped_arr[] = [
                        'db_field' => $db_field,
                        'excel_field_label' => $excel_field_label,
                    ];
                }

                $this->ImportMapper->deleteAll(['1' => '1']);
                $original = $this->ImportMapper->find()->toArray();
                $patched = $this->ImportMapper->patchEntities($original, $mapped_arr);
                if ($this->ImportMapper->saveMany($patched)) {
                    $response = ['status' => true, 'msg' => 'Mapped data save successfully'];
                }
            }
        }
        echo json_encode($response);
        die();
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
            $response = ['status' => false, 'msg' => implode(', ', $required_field_errors) . ' are mandatory'];
            echo json_encode($response);
            die();
        }

        $required_field_errors = $this->getRequiredFieldErrorsForListingData($post_data);

        if (!empty($required_field_errors)) {
            $response = ['status' => false, 'msg' => implode(', ', $required_field_errors) . ' are mandatory'];
            echo json_encode($response);
            die();
        }
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
        $this->viewBuilder()->setLayout('ajax');
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

        $this->session->write('listing_mapped_index', $listing_mapped_index);

        $item_per_page = self::ITEM_PER_PAGE;
        $list_data = array_slice($list_data, 0, $item_per_page);

        $pagination_setting = [
            'total_record' => $total_record,
            'item_per_page' => $item_per_page,
            'page' => 1
        ];
        $this->set(compact('pagination_setting', 'listing_mapped_index', 'list_heading', 'list_data'));
    }

    public function paging() {
         $this->viewBuilder()->setLayout('ajax');
         $list_data = [];
         $listing_mapped_index = [];
         if ($this->request->is("post") && $this->request->getData()) {
             $post_data = $this->request->getData();
             $item_per_page = $post_data['item_per_page'];
             $page = $post_data['page'];
             if ($page <= 0) {
                $page = 1;
             }
            $offset = ($page - 1) * $item_per_page;
            $import_session_data = $this->getImportedSessionData();
            $list_data = $import_session_data['list_data'];
            $list_data = array_slice($list_data, $offset, $item_per_page);
            $listing_mapped_index = $this->session->read('listing_mapped_index');
         }
         $this->set(compact('listing_mapped_index','list_data'));
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
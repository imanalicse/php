<?php
require '../../../vendor/autoload.php';

use App\Utils\Session;
use App\ImportExport\ImportWithFieldMapping\ImporterUtils;

$post_data = $_POST;
$import_utils = new ImporterUtils();
$response = $import_utils->saveFieldMapping($post_data);
echo json_encode($response);
<?php
require '../../vendor/autoload.php';

use App\ImportExport\ImportWithFieldMapping\ImporterUtils;

$import_utils = new ImporterUtils();
$response = $import_utils->downloadSampleFile();
<?php
require '../../vendor/autoload.php';

use App\FileHandler\ImportWithFieldMapping\ImporterUtils;

$import_utils = new ImporterUtils();
$response = $import_utils->downloadSampleFile();
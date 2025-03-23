<!DOCTYPE html>
<html>
<body>

<?php
    require 'phpspreadsheet/vendor/autoload.php';
    require 'ImportHandler.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;

    function uploadFiles($file, $path) {
        $target_file = $path . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $file["name"])). " has been uploaded.";
            return $file["name"];
        }
        return false;
    }

    if (isset($_FILES['csv_file']) && !empty($_FILES['csv_file'])) {
        $file = $_FILES['csv_file'];
        $upload_file_path = "uploads" . DIRECTORY_SEPARATOR;

        $file_extension = explode('.', $file["name"]);
        $file_extension = $file_extension[1];
        if ($file_extension == 'xls' || $file_extension == 'xlsx') {
            $file_name = uploadFiles($file, $upload_file_path);
            if ($file_name) {
                $uploaded_file_location = $upload_file_path . DIRECTORY_SEPARATOR . $file_name;

                $filetype = IOFactory::identify($file_name);

                // $reader = IOFactory::createReader("Xlsx");
                $reader = IOFactory::createReader($filetype);
                $spreadsheet = $reader->load($uploaded_file_location);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                $importHandler = new ImportHandler();
                $importHandler->saveImportedData($sheetData);
            }
        }
    }

?>


<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="csv_file"> <br/>
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>
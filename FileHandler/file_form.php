<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

include 'FileHandler.php';

if (!empty($_FILES)) {
    $file_handler = new FileHandler();
    $upload_file_path = __DIR__ . '/uploads/';
    $file = $_FILES['attachment'];
    $file_upload = $file_handler->uploadFile($file, $upload_file_path);
    echo "<pre>";
    print_r($file_upload);
    echo "</pre>";
}
?>
<form action="" enctype="multipart/form-data" method="post">
    <div>
        <label>Image</label>
        <input type="file" name="attachment">
    </div>
    <br>
    <input type="submit" value="Submit">
</form>


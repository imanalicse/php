<?php
//ini_set("memory_limit", "-1");

$file_directory = getcwd() . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
$zip_folder_name = 'test';
$zip_file_path = $file_directory . $zip_folder_name .'.zip';
if (file_exists($zip_file_path)) {
    @unlink($zip_file_path);
}

$zip = new ZipArchive;
if ($zip->open($zip_file_path, ZipArchive::CREATE) === TRUE) {
    $zip->addFile($file_directory . 'test.txt', 'new_test.txt'); // file path, file name in zip
    $zip->addFile($file_directory . 'sample.jpg', 'sample.jpg');
    //$zip->addFile($file_directory . '/video.mp4', 'video.mp4');
    echo "Number of files: " . $zip->numFiles . "<br/>";
    echo "Status: " . $zip->status . "<br/>";
    @$zip->close();
    echo 'ok';
} else {
    exit("cannot open <$zip_file_path>\n");
}